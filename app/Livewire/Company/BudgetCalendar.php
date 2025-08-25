<?php

namespace App\Livewire\Company;

use Carbon\Carbon;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyServiceContract;

class BudgetCalendar extends Component
{
    use WithPagination;

    public $selectedCompanyId;
    public $monthlyContracts = [];
    public $calendar;
    public $currentDate;
    public $dueDatesByDay = [];
    public $viewMode = 'month';
    public $selectedDate = null;
    public $selectedDueItems = [];

    public $search = '';
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'asc';
    public $allTags = [];
    public array $selectedTagFilters = [];


    public function mount()
    {
        $this->allTags = \App\Models\Tag::where('business_id', Auth::user()->current_business_id)
            ->get(['id', 'name']);

        $this->selectedTagFilters = session()->get('selected_tag_filters', []);

        $this->calendar = collect();
        $this->currentDate = Carbon::now();
        $this->viewMode = session('calendarViewMode', 'month');

        $this->selectedCompanyId = session('selectedCompanyId', $this->selectedCompanyId);

        $this->loadMonthlyContracts();
    }

    #[Computed]
    public function companies()
    {
        return Company::query()
            ->where('business_id', Auth::user()->current_business_id)
            ->when($this->selectedTagFilters, function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('tags.id', $this->selectedTagFilters);
                });
            })
            ->with(['contracts' => function ($q) {
                $q->when($this->search, function ($subQuery) {
                    $subQuery->whereHas('provider', function ($providerQuery) {
                        $providerQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhereHas('categories', function ($catQuery) {
                                $catQuery->where('name', 'like', '%' . $this->search . '%');
                            });
                    });
                })->with('provider', 'category');
            }])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function companyOptions()
    {
        return Company::query()
            ->where('business_id', Auth::user()->current_business_id)
            ->when($this->selectedTagFilters, function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('tags.id', $this->selectedTagFilters);
                });
            })
            ->orderBy('name')
            ->get(['id', 'name']);
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
        session(['selectedCompanyId' => $value ?: null]);
        $this->loadMonthlyContracts();
        $this->resetPage();
    }

    public function loadMonthlyContracts()
    {
        $this->dueDatesByDay = [];

        $companyIds = $this->selectedCompanyId
            ? [$this->selectedCompanyId]
            : Company::query()
            ->where('business_id', Auth::user()->current_business_id)
            ->when($this->selectedTagFilters, function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('tags.id', $this->selectedTagFilters);
                });
            })
            ->pluck('id')
            ->all();

        if (empty($companyIds)) {
            return;
        }

        $this->monthlyContracts = collect(range(1, 12))
            ->mapWithKeys(fn($m) => [$m => collect()])
            ->toArray();

        $contracts = CompanyServiceContract::with(['provider', 'category', 'reminders.contract'])
            ->whereIn('company_id', $companyIds)
            ->get();

        foreach ($contracts as $contract) {
            if ($contract->end_date) {
                $date = Carbon::parse($contract->end_date)->toDateString();
                $this->dueDatesByDay[$date][] = [
                    'type'     => 'contract',
                    'title'    => "{$contract->category->name} - {$contract->provider->name}",
                    'model'    => $contract,
                    'due_date' => $date,
                ];
            }

            foreach ($contract->reminders as $reminder) {
                $allDates = [];

                if ($reminder->due_date) {
                    $allDates[] = Carbon::parse($reminder->due_date)->toDateString();
                }

                if ($reminder->frequency === 'manual') {
                    $customDates = is_string($reminder->custom_dates)
                        ? json_decode($reminder->custom_dates, true)
                        : $reminder->custom_dates;

                    if (is_array($customDates)) {
                        foreach ($customDates as $customDate) {
                            $allDates[] = Carbon::parse($customDate)->toDateString();
                        }
                    }
                } else {
                    $baseDate     = $reminder->due_date ? Carbon::parse($reminder->due_date) : $this->currentDate;
                    $dayOfMonth   = $reminder->day_of_month ?? $baseDate->day;
                    $monthsActive = $reminder->months_active;

                    if (is_string($monthsActive)) {
                        $monthsActive = json_decode($monthsActive, true);
                    }

                    $monthsActive = is_array($monthsActive) && count($monthsActive) > 0 ? $monthsActive : range(1, 12);
                    $year = $this->currentDate->year;

                    foreach ($monthsActive as $month) {
                        try {
                            $date = Carbon::createFromDate($year, $month, $dayOfMonth)->toDateString();
                            $allDates[] = $date;
                        } catch (\Exception $e) {
                            // Skip invalid dates (Feb 30 etc.)
                        }
                    }
                }

                foreach (array_unique($allDates) as $date) {
                    $this->dueDatesByDay[$date][] = [
                        'type'     => 'reminder',
                        'title'    => $reminder->title,
                        'model'    => $reminder,
                        'due_date' => $date,
                    ];
                }
            }
        }
    }


    public function render()
    {
        return view('livewire.company.budget-calendar', [
            'companies' => $this->companies,
            'companyOptions' => $this->companyOptions,
        ]);
    }
}
