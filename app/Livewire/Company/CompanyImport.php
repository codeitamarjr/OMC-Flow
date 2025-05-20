<?php

namespace App\Livewire\Company;

use App\Models\Tag;
use App\Models\Company;
use Livewire\Component;
use Maatwebsite\Excel\Excel;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Imports\CompanyNumbersImport;
use App\Services\Core\CroSearchService;
use App\Jobs\CompanyFetchCroSubmissions;


class CompanyImport extends Component
{
    use WithFileUploads;

    public $file;
    public $imported = [];
    public $skipped = [];

    public $previewData = [];
    public $mode = 'upload';

    /**
     * Previews the contents of the uploaded file, validating the format and
     * looking up the company details in the CRO API.
     *
     * @return void
     */
    public function preview()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        $collection = app(Excel::class)
            ->toCollection(new CompanyNumbersImport, $this->file)
            ->first();
        $cro = app(CroSearchService::class);

        $this->previewData = [];

        foreach ($collection as $row) {
            $number = trim($row['company_number'] ?? '');
            $custom = trim($row['custom'] ?? '');
            $tags = trim($row['tags'] ?? '');

            if (!preg_match('/^\d{5,6}$/', $number)) {
                $this->previewData[] = [
                    'number' => $number,
                    'valid' => false,
                    'reason' => 'Invalid format',
                ];
                continue;
            }

            try {
                $details = $cro->getCompanyDetails($number);

                $this->previewData[] = [
                    'number' => $number,
                    'custom' => $custom,
                    'valid' => true,
                    'name' => $details['company_name'] ?? '',
                    'type' => $details['comp_type_desc'] ?? '',
                    'status' => $details['company_status_desc'] ?? '',
                    'reg_date' => $this->formatDate($details['company_reg_date'] ?? null),
                    'last_ar' => $this->formatDate($details['last_ar_date'] ?? null),
                    'next_ar' => $this->formatDate($details['next_ar_date'] ?? null),
                    'last_acc' => $this->formatDate($details['last_acc_date'] ?? null),
                    'address' => implode(', ', array_filter([
                        $details['company_addr_1'] ?? '',
                        $details['company_addr_2'] ?? '',
                        $details['company_addr_3'] ?? '',
                        $details['company_addr_4'] ?? '',
                        $details['eircode'] ?? '',
                    ])),
                    'place_of_business' => $details['place_of_business'] ?? '',
                    'company_type_code' => $details['company_type_code'] ?? null,
                    'company_status_code' => $details['company_status_code'] ?? null,
                    'raw' => $details, // stash full record for saving later
                    'tags' => $tags
                ];
            } catch (\Exception $e) {
                $this->previewData[] = [
                    'number' => $number,
                    'valid' => false,
                    'reason' => 'API error or not found',
                ];
            }
        }

        $this->mode = 'preview';
    }


    /**
     * Import all valid companies in the preview data.
     *
     * Creates a new Company model for each valid entry in the preview data,
     * skipping any that already exist. Fires a job to fetch the company's CRO
     * submissions after each import.
     */
    public function importAll()
    {
        $this->imported = [];
        $this->skipped = [];

        $businessId = Auth::user()->current_business_id;

        foreach ($this->previewData as $entry) {
            if (!$entry['valid']) {
                $this->skipped[] = [$entry['number'], $entry['reason']];
                continue;
            }

            $number = $entry['number'];
            $custom = $entry['custom'];
            $tags = $entry['tags'];

            if (Company::where('company_number', $number)->exists()) {
                $this->skipped[] = [$number, 'Already exists'];
                continue;
            }

            $data = $entry['raw'];

            $company = Company::create([
                'business_id' => Auth::user()->current_business_id,
                'company_number' => $number,
                'name' => strtoupper($data['company_name'] ?? ''),
                'custom' => strtoupper($custom ?? ''),
                'company_type' => $data['comp_type_desc'] ?? null,
                'status' => $data['company_status_desc'] ?? null,
                'effective_date' => $this->formatDate($data['company_status_date'] ?? null),
                'registration_date' => $this->formatDate($data['company_reg_date'] ?? null),
                'last_annual_return' => $this->formatDate($data['last_ar_date'] ?? null),
                'next_annual_return' => $this->formatDate($data['next_ar_date'] ?? null),
                'last_accounts' => $this->formatDate($data['last_acc_date'] ?? null),
                'postcode' => $data['eircode'] ?? null,
                'address_line_1' => $data['company_addr_1'] ?? null,
                'address_line_2' => $data['company_addr_2'] ?? null,
                'address_line_3' => $data['company_addr_3'] ?? null,
                'address_line_4' => $data['company_addr_4'] ?? null,
                'place_of_business' => $data['place_of_business'] ?? null,
                'company_type_code' => $data['company_type_code'] ?? null,
                'company_status_code' => $data['company_status_code'] ?? null,
            ]);

            $tagNames = array_filter(
                array_map('trim', explode('/', $tags)),
                fn($n) => $n !== ''
            );

            $tagIds = [];
            foreach ($tagNames as $rawName) {
                $name = strtoupper($rawName);

                $tag = Tag::firstOrCreate([
                    'business_id' => $businessId,
                    'name'        => $name,
                ]);

                $tagIds[] = $tag->id;
            }

            $company->tags()->syncWithoutDetaching($tagIds);


            // CompanyFetchCroSubmissions::dispatch($company);

            $this->imported[] = $number;
        }

        $this->mode = 'done';
        session()->flash('success', 'Companies imported.');
    }

    /**
     * Imports a company from the preview data at the given index.
     *
     * Checks the validity of the entry and ensures the company number does not
     * already exist in the database. If valid, creates a new company record
     * and flashes a success message. Otherwise, records the reason for skipping
     * and flashes an error message.
     *
     * @param int $index The index of the company entry in the preview data.
     */

    public function importCompany($index)
    {
        $entry = $this->previewData[$index] ?? null;

        if (!$entry || !$entry['valid']) {
            $this->skipped[] = [$entry['number'] ?? 'unknown', 'Invalid entry'];
            session()->flash('error', 'Invalid entry');
            return;
        }

        $number = $entry['number'];
        $custom = $entry['custom'];
        $tags = $entry['tags'];

        if (Company::where('company_number', $number)->exists()) {
            $this->skipped[] = [$number, 'Already exists'];
            session()->flash('error', 'Company already exists');
            return;
        }

        $data = $entry['raw'];

        $company = Company::create([
            'business_id' => Auth::user()->current_business_id,
            'company_number' => $number,
            'name' => strtoupper($data['company_name'] ?? ''),
            'custom' => strtoupper($custom ?? ''),
            'company_type' => $data['comp_type_desc'] ?? null,
            'status' => $data['company_status_desc'] ?? null,
            'effective_date' => $this->formatDate($data['company_status_date'] ?? null),
            'registration_date' => $this->formatDate($data['company_reg_date'] ?? null),
            'last_annual_return' => $this->formatDate($data['last_ar_date'] ?? null),
            'next_annual_return' => $this->formatDate($data['next_ar_date'] ?? null),
            'last_accounts' => $this->formatDate($data['last_acc_date'] ?? null),
            'postcode' => $data['eircode'] ?? null,
            'address_line_1' => $data['company_addr_1'] ?? null,
            'address_line_2' => $data['company_addr_2'] ?? null,
            'address_line_3' => $data['company_addr_3'] ?? null,
            'address_line_4' => $data['company_addr_4'] ?? null,
            'place_of_business' => $data['place_of_business'] ?? null,
            'company_type_code' => $data['company_type_code'] ?? null,
            'company_status_code' => $data['company_status_code'] ?? null,
        ]);

        if (strpos($tags, ',') !== false || strpos($tags, '/') !== false) {
            $tags = explode(',', $tags);
            $tags = array_map('trim', $tags);
        }

        foreach ($tags as $tag) {
            $company->tags()->attach($tag);
        }

        // CompanyFetchCroSubmissions::dispatch($company);

        $this->imported[] = $number;
        unset($this->previewData[$index]);
        $this->previewData = array_values($this->previewData);
        session()->flash('success', 'Company imported.');
    }

    /**
     * Converts an ISO date string to a 'Y-m-d' format.
     *
     * Returns null if the input is empty or '0001-01-01T00:00:00Z' (the default value returned by the CRO API).
     *
     * @param string|null $iso The ISO date string.
     * @return string|null The formatted date string.
     */
    protected function formatDate(?string $iso): ?string
    {
        if (!$iso || $iso === '0001-01-01T00:00:00Z') {
            return null;
        }

        return \Carbon\Carbon::parse($iso)->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.company.company-import');
    }
}
