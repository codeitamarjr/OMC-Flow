<?php

namespace App\Livewire\Company\Service;

use App\Models\Company;
use Livewire\Component;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ContractReminder;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyServiceContract;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContractManager extends Component
{
    use AuthorizesRequests;

    public $companies;
    public $categories;
    public $providers;
    public $contracts;

    public $company_id;
    public $service_category_id;
    public $service_provider_id;
    public $budget;
    public $start_date;
    public $end_date;
    public $status = 'active';
    public $notes;

    public bool $showCreateModal = false;

    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public ?CompanyServiceContract $editingContract = null;
    public ?CompanyServiceContract $deletingContract = null;

    // Reminder properties
    public bool $showReminderModal = false;
    public ?int $contractReminderContractId = null;
    public $reminder_title, $reminder_due_date, $reminder_frequency = 'manual';
    public $reminder_day_of_month, $reminder_custom_dates = [], $reminder_months_active = [];
    public $reminder_days_before = 7, $reminder_days_after = 0, $reminder_notes;
    public string $reminder_months_active_string = '';
    public string $reminder_custom_dates_string = '';
    public ?int $reminder_id = null;


    public function mount()
    {
        $this->companies = Company::where('business_id', Auth::user()->current_business_id)->orderBy('name')->get();
        $this->categories = ServiceCategory::where('business_id', Auth::user()->current_business_id)->get()->sortBy('name');
        $this->providers = collect();
        $this->loadContracts();
    }

    public function updatedServiceCategoryId($categoryId)
    {
        $category = ServiceCategory::with('providers')->find($categoryId);
        $this->providers = $category?->providers ?? collect();
        $this->service_provider_id = null;
    }

    public function loadContracts()
    {
        $this->contracts = CompanyServiceContract::with(['company', 'provider', 'category', 'reminders'])
            ->whereHas('company', fn($q) => $q->where('business_id', Auth::user()->current_business_id))
            ->latest()->get();
    }

    public function openCreateModal()
    {
        $this->reset(['company_id', 'service_category_id', 'service_provider_id', 'budget', 'start_date', 'end_date', 'status', 'notes']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function create()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'budget' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        CompanyServiceContract::create([
            'company_id' => $this->company_id,
            'service_category_id' => $this->service_category_id,
            'service_provider_id' => $this->service_provider_id,
            'budget' => $this->budget,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->reset(['showCreateModal']);
        $this->loadContracts();
        session()->flash('success', 'Service contract created.');
    }

    public function edit($id)
    {
        $this->editingContract = CompanyServiceContract::findOrFail($id);

        $this->company_id = $this->editingContract->company_id;
        $this->service_category_id = $this->editingContract->service_category_id;
        $this->updatedServiceCategoryId($this->service_category_id); // Load related providers
        $this->service_provider_id = $this->editingContract->service_provider_id;
        $this->budget = $this->editingContract->budget;
        $this->start_date = $this->editingContract->start_date;
        $this->end_date = $this->editingContract->end_date;
        $this->status = $this->editingContract->status;
        $this->notes = $this->editingContract->notes;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'budget' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        if (! $this->editingContract) return;

        $this->editingContract->update([
            'company_id' => $this->company_id,
            'service_category_id' => $this->service_category_id,
            'service_provider_id' => $this->service_provider_id,
            'budget' => $this->budget,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->showEditModal = false;
        $this->editingContract = null;
        $this->loadContracts();
        session()->flash('success', 'Contract updated.');
    }

    public function confirmDelete($id)
    {
        $this->deletingContract = CompanyServiceContract::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if (! $this->deletingContract) return;

        $this->deletingContract->delete();
        $this->deletingContract = null;
        $this->showDeleteModal = false;
        $this->loadContracts();
        session()->flash('success', 'Contract deleted.');
    }

    public function openReminderModal($contractIdToAttachToReminder)
    {
        $this->reset([
            'reminder_title',
            'reminder_due_date',
            'reminder_frequency',
            'reminder_day_of_month',
            'reminder_custom_dates',
            'reminder_months_active',
            'reminder_days_before',
            'reminder_days_after',
            'reminder_notes',
            'reminder_id',
        ]);

        $this->contractReminderContractId = $contractIdToAttachToReminder;
        $this->showReminderModal = true;
    }

    public function saveReminder()
    {
        $months = collect(explode(',', $this->reminder_months_active_string))
            ->filter()
            ->map(fn($m) => (int) trim($m))
            ->filter(fn($m) => $m >= 1 && $m <= 12)
            ->values()
            ->toArray();

        $dates = collect(explode(',', $this->reminder_custom_dates_string))
            ->map(fn($d) => trim($d))
            ->filter(function ($d) {
                try {
                    return \Carbon\Carbon::createFromFormat('Y-m-d', $d) !== false;
                } catch (\Exception $e) {
                    return false;
                }
            })
            ->values()
            ->toArray();

        $data = [
            'company_service_contract_id' => $this->contractReminderContractId,
            'title' => $this->reminder_title,
            'due_date' => $this->reminder_due_date,
            'frequency' => $this->reminder_frequency,
            'day_of_month' => $this->reminder_day_of_month,
            'custom_dates' => $dates,
            'months_active' => $months,
            'reminder_days_before' => $this->reminder_days_before,
            'reminder_days_after' => $this->reminder_days_after,
            'notes' => $this->reminder_notes,
        ];

        if ($this->reminder_id) {
            ContractReminder::find($this->reminder_id)?->update($data);
            session()->flash('success', 'Reminder updated.');
        } else {
            ContractReminder::create($data);
            session()->flash('success', 'Reminder created.');
        }

        $this->reset(['showReminderModal', 'reminder_id']);
        $this->loadContracts();
    }

    public function editReminder($reminderId)
    {
        $reminder = ContractReminder::findOrFail($reminderId);

        $this->reminder_id = $reminder->id;
        $this->contractReminderContractId = $reminder->company_service_contract_id;

        $this->reminder_title = $reminder->title;
        $this->reminder_due_date = $reminder->due_date;
        $this->reminder_frequency = $reminder->frequency;
        $this->reminder_day_of_month = $reminder->day_of_month;
        $this->reminder_custom_dates_string = collect($reminder->custom_dates)->implode(',');
        $this->reminder_months_active_string = collect($reminder->months_active)->implode(',');
        $this->reminder_days_before = $reminder->reminder_days_before;
        $this->reminder_days_after = $reminder->reminder_days_after;
        $this->reminder_notes = $reminder->notes;

        $this->showReminderModal = true;
    }

    public function render()
    {
        return view('livewire.company.service.contract-manager');
    }
}
