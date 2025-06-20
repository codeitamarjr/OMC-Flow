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
    public $selectedDate = null;
    public $selectedDueItems = [];


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

    public function showDueItems($date)
    {
        $this->selectedDate = $date;
        $this->selectedDueItems = $this->dueDatesByDay[$date] ?? [];
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

        $contracts = CompanyServiceContract::with(['provider', 'category', 'reminders.contract'])
            ->where('company_id', $this->selectedCompanyId)
            ->get();

        foreach ($contracts as $contract) {
            // Add contract end_date (if any)
            if ($contract->end_date) {
                $date = Carbon::parse($contract->end_date)->toDateString();
                $this->dueDatesByDay[$date][] = [
                    'type' => 'contract',
                    'title' => "{$contract->category->name} - {$contract->provider->name}",
                    'model' => $contract,
                    'due_date' => $date,
                ];
            }


            foreach ($contract->reminders as $reminder) {
                $allDates = [];

                // Always include the primary due_date first
                if ($reminder->due_date) {
                    $allDates[] = Carbon::parse($reminder->due_date)->toDateString();
                }

                // Manual reminders with custom dates
                if ($reminder->frequency === 'manual') {
                    $customDates = is_string($reminder->custom_dates) ? json_decode($reminder->custom_dates) : $reminder->custom_dates;

                    if (is_array($customDates)) {
                        foreach ($customDates as $customDate) {
                            $allDates[] = Carbon::parse($customDate)->toDateString();
                        }
                    }
                } else {
                    // Derive values from due_date if others are not set
                    $baseDate = $reminder->due_date ? Carbon::parse($reminder->due_date) : $this->currentDate;
                    $dayOfMonth = $reminder->day_of_month ?? $baseDate->day;
                    $monthsActive = $reminder->months_active;

                    // Convert JSON string to array if needed
                    if (is_string($monthsActive)) {
                        $monthsActive = json_decode($monthsActive, true);
                    }

                    // Default to all months if not set or invalid
                    $monthsActive = is_array($monthsActive) && count($monthsActive) > 0 ? $monthsActive : range(1, 12);

                    $year = $this->currentDate->year;

                    foreach ($monthsActive as $month) {
                        try {
                            $date = Carbon::createFromDate($year, $month, $dayOfMonth)->toDateString();
                            $allDates[] = $date;
                        } catch (\Exception $e) {
                            // Skip invalid dates like Feb 30
                        }
                    }
                }

                // Add all to dueDatesByDay
                foreach (array_unique($allDates) as $date) {
                    $this->dueDatesByDay[$date][] = [
                        'type' => 'reminder',
                        'title' => $reminder->title,
                        'model' => $reminder,
                        'due_date' => $date,
                    ];
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.company.budget-calendar');
    }
}
