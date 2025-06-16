<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyServiceContract;

class BudgetForecast extends Component
{
    public $companies;
    public $selectedCompanyId;
    public $contracts = [];

    public function mount()
    {
        $this->companies = Company::where('business_id', Auth::user()->current_business_id)
            ->orderBy('name')
            ->get();

        $this->selectedCompanyId = null;
    }

    public function updatedSelectedCompanyId($value)
    {
        $this->loadContracts();
    }

    public function loadContracts()
    {
        if ($this->selectedCompanyId) {
            $this->contracts = CompanyServiceContract::with(['category', 'provider'])
                ->where('company_id', $this->selectedCompanyId)
                ->get();
        } else {
            $this->contracts = [];
        }
    }
    public function render()
    {
        return view('livewire.company.budget-forecast');
    }
}
