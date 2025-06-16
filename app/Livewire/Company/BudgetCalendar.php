<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyServiceContract;
use Carbon\Carbon;

class BudgetCalendar extends Component
{
    public $companies;
    public $selectedCompanyId;
    public $monthlyContracts = [];
    public $calendar;
    public $currentDate;
    public $dueDatesByDay = [];
    public $viewMode = 'month';


    public function mount()
    {
        $this->companies = Company::where('business_id', Auth::user()->current_business_id)->orderBy('name')->get();
        $this->calendar = collect();
        $this->currentDate = Carbon::now();
        $this->selectedCompanyId = session('selectedCompanyId');
        $this->viewMode = session('calendarViewMode', 'month');

        if ($this->selectedCompanyId) {
            $this->loadMonthlyContracts();
        }
    }

    public function updatedViewMode($value)
    {
        session(['calendarViewMode' => $value]);
    }

    public function goToPreviousMonth()
    {
        $this->currentDate = $this->currentDate->copy()->subMonth();
    }

    public function goToNextMonth()
    {
        $this->currentDate = $this->currentDate->copy()->addMonth();
    }

    public function goToToday()
    {
        $this->currentDate = Carbon::now();
    }


    public function updatedSelectedCompanyId($value)
    {
        session(['selectedCompanyId' => $value]);
        $this->loadMonthlyContracts();
    }

    public function loadMonthlyContracts()
    {
        $this->dueDatesByDay = [];
        if (!$this->selectedCompanyId) return;

        $this->monthlyContracts = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => collect()];
        })->toArray();

        if (!$this->selectedCompanyId) return;

        $contracts = CompanyServiceContract::with(['provider', 'category'])
            ->where('company_id', $this->selectedCompanyId)
            ->get();

        foreach ($contracts as $contract) {
            $date = Carbon::parse($contract->next_due_date)->toDateString();
            $this->dueDatesByDay[$date][] = $contract;
        }
    }

    public function render()
    {
        return view('livewire.company.budget-calendar');
    }
}
