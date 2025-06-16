<?php

namespace App\Livewire\Company\Service;

use App\Models\Company;
use Livewire\Component;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
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
    public $next_due_date;
    public $status = 'active';
    public $notes;

    public bool $showCreateModal = false;

    public function mount()
    {
        $this->companies = Company::where('business_id', Auth::user()->current_business_id)->orderBy('name')->get();
        $this->categories = ServiceCategory::where('business_id', Auth::user()->current_business_id)->get();
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
        $this->contracts = CompanyServiceContract::with(['company', 'provider', 'category'])
            ->whereHas('company', fn($q) => $q->where('business_id', Auth::user()->current_business_id))
            ->latest()->get();
    }

    public function openCreateModal()
    {
        $this->reset(['company_id', 'service_category_id', 'service_provider_id', 'budget', 'start_date', 'next_due_date', 'status', 'notes']);
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
            'next_due_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        CompanyServiceContract::create([
            'company_id' => $this->company_id,
            'service_category_id' => $this->service_category_id,
            'service_provider_id' => $this->service_provider_id,
            'budget' => $this->budget,
            'start_date' => $this->start_date,
            'next_due_date' => $this->next_due_date,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->reset(['showCreateModal']);
        $this->loadContracts();
        session()->flash('success', 'Service contract created.');
    }
    public function render()
    {
        return view('livewire.company.service.contract-manager');
    }
}
