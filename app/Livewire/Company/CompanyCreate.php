<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Jobs\CompanyFetchCroSubmissions;

class CompanyCreate extends Component
{
    public string $company_number = '';
    public string $name = '';
    public ?string $custom = null;
    public ?string $company_type = null;
    public ?string $status = null;
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

    protected $rules = [
        'company_number' => 'required|string|unique:companies,company_number',
        'name' => 'required|string|max:255',
        'custom' => 'nullable|string',
        'company_type' => 'nullable|string',
        'status' => 'nullable|string',
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

    /**
     * Updates company fields based on the company number.
     *
     * Validates the format of the company number and retrieves company
     * details from the CRO if valid. If the number is invalid or no
     * matches are found, resets the company fields. Handles any
     * exceptions that occur during the process and resets fields while
     * displaying an error message.
     *
     * @param string $value The company number being updated.
     */
    public function updatedCompanyNumber($value)
    {

        if (!preg_match('/^\d{5,6}$/', $value)) {
            $this->resetCompanyFields();
            return;
        }

        try {
            $cro = app(\App\Services\Core\CroSearchService::class);

            $matches = $cro->searchByNumber($value);

            if (count($matches) === 0) {
                $this->resetCompanyFields();
                return;
            }

            $details = $cro->getCompanyDetails($value);

            $this->name = strtoupper($details['company_name'] ?? '');
            $this->company_type = $details['comp_type_desc'] ?? null;
            $this->status = $details['company_status_desc'] ?? null;
            $this->effective_date = $this->formatDate($details['company_status_date'] ?? null);
            $this->registration_date = $this->formatDate($details['company_reg_date'] ?? null);
            $this->last_annual_return = $this->formatDate($details['last_ar_date'] ?? null);
            $this->next_annual_return = $this->formatDate($details['next_ar_date'] ?? null);
            $this->next_financial_statement_due = $this->formatDate($details['next_fs_due_date'] ?? null);
            $this->last_accounts = $this->formatDate($details['last_acc_date'] ?? null);
            $this->last_agm = $this->formatDate($details['last_agm_date'] ?? null);
            $this->financial_year_end = $this->formatDate($details['financial_year_end'] ?? null);
            $this->postcode = strtoupper($details['eircode'] ?? null);
            $this->address_line_1 = strtoupper($details['company_addr_1'] ?? null);
            $this->address_line_2 = strtoupper($details['company_addr_2'] ?? null);
            $this->address_line_3 = strtoupper($details['company_addr_3'] ?? null);
            $this->address_line_4 = strtoupper($details['company_addr_4'] ?? null);
            $this->place_of_business = strtoupper($details['place_of_business'] ?? null);
            $this->company_type_code = $details['company_type_code'] ?? null;
            $this->company_status_code = $details['company_status_code'] ?? null;
        } catch (\Exception $e) {
            $this->resetCompanyFields();
            session()->flash('error', 'CRO API error: ' . $e->getMessage());
        }
    }

    /**
     * Reset all company fields to their default state.
     *
     * This is useful when the user enters an invalid company number,
     * or when the API call to the CRO fails.
     */
    protected function resetCompanyFields()
    {
        $this->name = '';
        $this->custom = null;
        $this->company_type = null;
        $this->status = null;
        $this->effective_date = null;
        $this->registration_date = null;
        $this->last_annual_return = null;
        $this->next_annual_return = null;
        $this->next_financial_statement_due = null;
        $this->last_accounts = null;
        $this->last_agm = null;
        $this->financial_year_end = null;
        $this->postcode = null;
        $this->address_line_1 = null;
        $this->address_line_2 = null;
        $this->address_line_3 = null;
        $this->address_line_4 = null;
        $this->place_of_business = null;
        $this->company_type_code = null;
        $this->company_status_code = null;
    }

    public function save()
    {
        $this->validate();

        $company = Company::create([
            'business_id' => Auth::user()->current_business_id,
            'company_number' => $this->company_number,
            'name' => $this->name,
            'custom' => $this->custom,
            'company_type' => $this->company_type,
            'status' => $this->status,
            'effective_date' => $this->effective_date,
            'registration_date' => $this->registration_date,
            'last_annual_return' => $this->last_annual_return,
            'next_annual_return' => $this->next_annual_return,
            'next_financial_statement_due' => $this->next_financial_statement_due,
            'last_accounts' => $this->last_accounts,
            'last_agm' => $this->last_agm,
            'financial_year_end' => $this->financial_year_end,
            'postcode' => $this->postcode,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'address_line_3' => $this->address_line_3,
            'address_line_4' => $this->address_line_4,
            'place_of_business' => $this->place_of_business,
            'company_type_code' => $this->company_type_code,
            'company_status_code' => $this->company_status_code,
        ]);
        // CompanyFetchCroSubmissions::dispatch($company);

        $this->reset([
            'company_number',
            'name',
            'custom',
            'company_type',
            'status',
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
        ]);

        session()->flash('success', 'Company created successfully.');
        $this->dispatch('company-created');

        redirect()->route('company.manage');
    }

    /**
     * Converts an ISO date string to a 'Y-m-d' format.
     *
     * Returns null if the input is empty or '0001-01-01T00:00:00Z' (the default value returned by the CRO API).
     *
     * @param string|null $isoDate The ISO date string.
     * @return string|null The formatted date string.
     */
    protected function formatDate(?string $isoDate): ?string
    {
        if (!$isoDate || $isoDate === '0001-01-01T00:00:00Z') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($isoDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function render()
    {
        return view('livewire.company.company-create');
    }
}
