<?php

namespace App\Livewire\Company;

use App\Models\Company as CompanyModel;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CompanyEdit extends Component
{
    public CompanyModel $company;

    public string $company_number;
    public string $name;
    public ?string $custom = null;
    public ?string $company_type = null;
    public ?string $status = null;
    public ?string $effective_date = null;
    public ?string $registration_date = null;
    public ?string $last_annual_return = null;
    public ?string $next_annual_return = null;
    public ?string $next_financial_statement_due = null;
    public ?string $last_accounts = null;
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
            'effective_date' => 'nullable|date',
            'registration_date' => 'nullable|date',
            'last_annual_return' => 'nullable|date',
            'next_annual_return' => 'nullable|date',
            'next_financial_statement_due' => 'nullable|date',
            'last_accounts' => 'nullable|date',
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
            'effective_date',
            'registration_date',
            'last_annual_return',
            'next_annual_return',
            'next_financial_statement_due',
            'last_accounts',
            'postcode',
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'address_line_4',
            'place_of_business',
            'company_type_code',
            'company_status_code',
        ]));
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
            'effective_date' => $this->effective_date,
            'registration_date' => $this->registration_date,
            'last_annual_return' => $this->last_annual_return,
            'next_annual_return' => $this->next_annual_return,
            'next_financial_statement_due' => $this->next_financial_statement_due,
            'last_accounts' => $this->last_accounts,
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

    public function render()
    {
        return view('livewire.company.company-edit');
    }
}
