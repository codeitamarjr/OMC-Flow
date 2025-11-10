<?php

namespace App\Livewire\Company;

use App\Models\Company as CompanyModel;
use App\Services\Core\CroSearchService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Throwable;

class CompanyEdit extends Component
{
    public CompanyModel $company;

    public string $company_number;
    public string $name;
    public ?string $custom = null;
    public ?string $company_type = null;
    public ?string $status = null;
    public ?bool $active = null;
    public ?string $effective_date = null;
    public ?string $registration_date = null;
    public ?string $last_annual_return = null;
    public ?string $next_annual_return = null;
    public ?string $next_financial_statement_due = null;
    public ?string $last_accounts = null;
    public ?string $last_agm = null;
    public ?string $financial_year_end = null;
    public ?string $postcode = null;
    public ?string $address_line_1 = null;
    public ?string $address_line_2 = null;
    public ?string $address_line_3 = null;
    public ?string $address_line_4 = null;
    public ?string $place_of_business = null;
    public ?int $company_type_code = null;
    public ?int $company_status_code = null;

    protected function rules(): array
    {
        return [
            'company_number' => 'required|string|unique:companies,company_number,' . $this->company->id,
            'name' => 'required|string|max:255',
            'custom' => 'nullable|string',
            'company_type' => 'nullable|string',
            'status' => 'nullable|string',
            'active' => 'nullable|boolean',
            'effective_date' => 'nullable|date',
            'registration_date' => 'nullable|date',
            'last_annual_return' => 'nullable|date',
            'next_annual_return' => 'nullable|date',
            'next_financial_statement_due' => 'nullable|date',
            'last_accounts' => 'nullable|date',
            'last_agm' => 'nullable|date',
            'financial_year_end' => 'nullable|date',
            'postcode' => 'nullable|string|max:1000',
            'address_line_1' => 'nullable|string|max:1000',
            'address_line_2' => 'nullable|string|max:1000',
            'address_line_3' => 'nullable|string|max:1000',
            'address_line_4' => 'nullable|string|max:1000',
            'place_of_business' => 'nullable|string|max:1000',
            'company_type_code' => 'nullable|integer',
            'company_status_code' => 'nullable|integer',
        ];
    }

    protected array $dateFields = [
        'effective_date',
        'registration_date',
        'last_annual_return',
        'next_annual_return',
        'next_financial_statement_due',
        'last_accounts',
        'last_agm',
        'financial_year_end',
    ];

    protected array $croDateFieldMap = [
        'effective_date' => 'company_status_date',
        'registration_date' => 'company_reg_date',
        'last_annual_return' => 'last_ar_date',
        'next_annual_return' => 'next_ar_date',
        'next_financial_statement_due' => 'next_fs_due_date',
        'last_accounts' => 'last_acc_date',
        'last_agm' => 'last_agm_date',
        'financial_year_end' => 'financial_year_end',
    ];

    public function mount(CompanyModel $company)
    {
        if ($company->business_id !== Auth::user()->current_business_id) {
            abort(403);
        }

        $this->company = $company;
        $this->fill($company->only([
            'company_number',
            'name',
            'custom',
            'company_type',
            'status',
            'active',
            'effective_date',
            'registration_date',
            'last_annual_return',
            'next_annual_return',
            'next_financial_statement_due',
            'last_accounts',
            'last_agm',
            'financial_year_end',
            'postcode',
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'address_line_4',
            'place_of_business',
            'company_type_code',
            'company_status_code',
        ]));

        foreach ($this->dateFields as $field) {
            $this->{$field} = $this->formatDateForInput($company->{$field});
        }
    }

    public function save()
    {
        $this->validate();

        $this->company->update([
            'company_number' => $this->company_number,
            'name' => $this->name,
            'custom' => $this->custom,
            'company_type' => $this->company_type,
            'status' => $this->status,
            'active' => $this->active,
            'effective_date' => $this->formatDateForPersistence($this->effective_date),
            'registration_date' => $this->formatDateForPersistence($this->registration_date),
            'last_annual_return' => $this->formatDateForPersistence($this->last_annual_return),
            'next_annual_return' => $this->formatDateForPersistence($this->next_annual_return),
            'next_financial_statement_due' => $this->formatDateForPersistence($this->next_financial_statement_due),
            'last_accounts' => $this->formatDateForPersistence($this->last_accounts),
            'last_agm' => $this->formatDateForPersistence($this->last_agm),
            'financial_year_end' => $this->formatDateForPersistence($this->financial_year_end),
            'postcode' => $this->postcode,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'address_line_3' => $this->address_line_3,
            'address_line_4' => $this->address_line_4,
            'place_of_business' => $this->place_of_business,
            'company_type_code' => $this->company_type_code,
            'company_status_code' => $this->company_status_code,
        ]);

        session()->flash('success', 'Company updated.');
        $this->dispatch('company-updated');

        redirect()->route('company.manage');
    }

    public function refreshFromCro(): void
    {
        if (!preg_match('/^\d{5,6}$/', $this->company_number)) {
            session()->flash('error', 'Enter a valid company number before refreshing from the CRO.');
            return;
        }

        try {
            /** @var CroSearchService $cro */
            $cro = app(CroSearchService::class);

            $matches = $cro->searchByNumber($this->company_number);

            if (count($matches) === 0) {
                session()->flash('error', 'No CRO records found for that company number.');
                return;
            }

            $details = $cro->getCompanyDetails($this->company_number);

            if (empty($details)) {
                session()->flash('error', 'CRO returned an empty response for that company number.');
                return;
            }

            $this->applyCroDetails($details);

            session()->flash('success', 'Company details refreshed from the CRO. Review and save to persist the changes.');
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'CRO API error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.company.company-edit');
    }

    protected function formatDateForInput($value): ?string
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    protected function formatDateForPersistence(?string $value): ?string
    {
        return $value ?: null;
    }

    protected function applyCroDetails(array $details): void
    {
        $name = $this->uppercase($details['company_name'] ?? null);

        if ($name !== null) {
            $this->name = $name;
        }

        $this->company_type = $details['comp_type_desc'] ?? null;
        $this->status = $details['company_status_desc'] ?? null;

        foreach ($this->croDateFieldMap as $property => $sourceKey) {
            $this->{$property} = $this->formatCroDate($details[$sourceKey] ?? null);
        }

        $this->postcode = $this->uppercase($details['eircode'] ?? null);
        $this->address_line_1 = $this->uppercase($details['company_addr_1'] ?? null);
        $this->address_line_2 = $this->uppercase($details['company_addr_2'] ?? null);
        $this->address_line_3 = $this->uppercase($details['company_addr_3'] ?? null);
        $this->address_line_4 = $this->uppercase($details['company_addr_4'] ?? null);
        $this->place_of_business = $this->uppercase($details['place_of_business'] ?? null);

        $this->company_type_code = $details['company_type_code'] ?? null;
        $this->company_status_code = $details['company_status_code'] ?? null;
    }

    protected function formatCroDate(?string $isoDate): ?string
    {
        if (!$isoDate || $isoDate === '0001-01-01T00:00:00Z') {
            return null;
        }

        try {
            return Carbon::parse($isoDate)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    protected function uppercase(?string $value): ?string
    {
        return $value === null ? null : strtoupper($value);
    }
}
