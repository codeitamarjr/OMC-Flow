<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyServiceContract;

class BudgetCalendar extends Component
{
    public $companies;
    public $selectedCompanyId;
    public $monthlyContracts = [];
    public $calendar;

    public function mount()
    {
        $this->companies = Company::where('business_id', Auth::user()->current_business_id)->orderBy('name')->get();
        $this->calendar = collect();
    }

    public function updatedSelectedCompanyId($value)
    {
        $this->loadMonthlyContracts();
    }

    public function loadMonthlyContracts()
    {
        $this->monthlyContracts = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => collect()];
        })->toArray();

        if (!$this->selectedCompanyId) return;

        $contracts = CompanyServiceContract::with(['provider', 'category'])
            ->where('company_id', $this->selectedCompanyId)
            ->get();

        foreach ($contracts as $contract) {
            if ($contract->next_due_date) {
                $month = date('n', strtotime($contract->next_due_date));
                $this->monthlyContracts[$month][] = $contract;
            }
        }
    }

    public function render()
    {
        return view('livewire.company.budget-calendar');
    }
}
