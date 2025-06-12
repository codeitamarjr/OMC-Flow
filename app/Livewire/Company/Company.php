<?php

namespace App\Livewire\Company;

use Livewire\Component;
use App\Models\Business;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Jobs\CompanyFetchCroSubmissions;
use App\Models\Company as ModelsCompany;
use Illuminate\Database\Eloquent\Model;

class Company extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 20;
    public $sortBy = 'next_annual_return';
    public $sortDirection = 'asc';
    public $allTags = [];
    public array $selectedTagFilters = [];
    public ?ModelsCompany $selectedCompany = null;
    public bool $showDetailsModal = false;
    public ?array $croDefinitions = null;
    public bool $showCroDefinitionsModal = false;
    public array $financialYearEnds = [];
    public array $lastAGMs = [];

    public $companyDetailsModal = false;
    public ?ModelsCompany $viewingCompany = null;


    /**
     * Perform initialisation when the component is mounted.
     *
     * 1. Check if the current business ID has changed since the last request.
     *    If it has, forget the selected tag filters and store the new current business ID.
     * 2. Retrieve all tags associated with the current business.
     * 3. Retrieve the selected tag filters from the session.
     * 4. Set the financial year end and last AGM dates for each company associated
     *    with the current business.
     *
     * @return void
     */
    public function mount()
    {
        $currentBusinessId = Auth::user()->current_business_id;

        if (session('last_business_id') !== $currentBusinessId) {
            session()->forget('selected_tag_filters');
            session()->put('last_business_id', $currentBusinessId);
        }

        $this->allTags = \App\Models\Tag::where('business_id', Auth::user()->current_business_id)
            ->get(['id', 'name']);

        $this->selectedTagFilters = session()->get('selected_tag_filters', []);

        foreach (Auth::user()->currentBusiness->companies as $company) {
            $this->financialYearEnds[$company->id] = $company->financial_year_end;
            $this->lastAGMs[$company->id] = $company->last_agm;
        }
    }

    /**
     * Retrieve a paginated list of companies associated with the current business.
     * The list can be filtered by search keyword and selected tag filters, and is
     * sorted by the specified column and direction.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */

    #[Computed]
    public function companies()
    {
        return Business::find(Auth::user()->currentBusiness->id)->companies()
            ->with('croDocDefinitions')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('custom', 'like', '%' . $this->search . '%')
                        ->orWhere('company_number', 'like', '%' . $this->search . '%')
                        ->orWhere('address_line_1', 'like', '%' . $this->search . '%')
                        ->orWhere('address_line_2', 'like', '%' . $this->search . '%')
                        ->orWhere('address_line_3', 'like', '%' . $this->search . '%')
                        ->orWhere('address_line_4', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedTagFilters, function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('tags.id', $this->selectedTagFilters);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /**
     * Sorts the companies by the specified column.
     * Toggles the sorting direction between ascending and descending
     * if the same column is selected consecutively.
     *
     * @param string $column The column to sort by.
     */
    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Store the selected tag filters in the session when they change.
     *
     * This will cause the companies list to be re-filtered when the selected
     * tag filters are updated.
     */
    public function updatedSelectedTagFilters()
    {
        session()->put('selected_tag_filters', $this->selectedTagFilters);
    }

    /**
     * Saves the financial year end for the given company ID.
     *
     * @param int $companyId The ID of the company to save the financial year end for.
     *
     * @return void
     */
    public function saveFinancialYearEnd($companyId)
    {
        $date = $this->financialYearEnds[$companyId] ?? null;

        if ($date) {
            $company = \App\Models\Company::findOrFail($companyId);

            abort_unless(
                Auth::user()->businesses()->where('business_id', $company->business_id)->exists(),
                403,
                'Unauthorized'
            );

            $company->update(['financial_year_end' => $date]);

            session()->flash('success', 'Financial year end saved.');
        }
    }

    /**
     * Update the last AGM date for the given company ID.
     *
     * @param int $companyId The ID of the company to update.
     *
     * @return void
     */
    public function saveLastAGM($companyId)
    {
        $date = $this->lastAGMs[$companyId] ?? null;

        if ($date) {
            $company = \App\Models\Company::findOrFail($companyId);

            abort_unless(
                Auth::user()->businesses()->where('business_id', $company->business_id)->exists(),
                403,
                'Unauthorized'
            );

            $company->update(['last_agm' => $date]);

            session()->flash('success', 'Last AGM saved.');
        }
    }
    /**
     * Loads and displays the submission documents for the specified company.
     *
     * Finds the company by its ID and loads its submission documents. Dispatches a job
     * to update the company's submission documents from the CRO API. Opens the modal
     * to show the details of the company's submissions.
     *
     * @param int $id The ID of the company whose submission documents are to be shown.
     */
    public function showCompanySubmissions($id)
    {
        $this->selectedCompany = ModelsCompany::findOrFail($id)->load('submissionDocuments');

        CompanyFetchCroSubmissions::dispatch($this->selectedCompany);

        $this->selectedCompany->load('submissionDocuments');
        $this->showDetailsModal = true;
    }

    public function showCroDefinition(ModelsCompany $company)
    {
        abort_unless(
            Auth::user()->businesses()->where('business_id', $company->business_id)->exists(),
            403,
            'You do not have permission to view this company'
        );

        $this->selectedCompany = $company;

        $this->croDefinitions = $company->croDocDefinitions->all();
        $this->showCroDefinitionsModal = true;
    }

    /**
     * Toggles the completion status of a CRO document definition for the selected company.
     *
     * Finds the specified CRO document definition by its ID in the selected company's
     * list of document definitions. Flips its completion status and updates the 
     * pivot table with the new status, timestamp, and user ID if completed.
     * Reloads the company's CRO document definitions after the update.
     *
     * @param int $definitionId The ID of the CRO document definition to toggle.
     */
    public function toggleCroDocument(int $definitionId)
    {
        $company = $this->selectedCompany;

        $current = $company->croDocDefinitions
            ->first(fn($d) => $d->id === $definitionId)
            ->pivot
            ->completed;

        $new = ! $current;

        $company->croDocDefinitions()
            ->updateExistingPivot($definitionId, [
                'completed'    => $new,
                'completed_at' => $new ? now() : null,
                'completed_by' => $new ? Auth::id() : null,
            ]);

        $this->selectedCompany->load('croDocDefinitions');
    }

    public function viewCompanyDetails(ModelsCompany $company)
    {
        abort_unless(
            Auth::user()->businesses()->where('business_id', $company->business_id)->exists(),
            403,
            'You do not have permission to view this company'
        );

        $this->viewingCompany = $company;
        $this->companyDetailsModal = true;
    }


    public function render()
    {
        return view('livewire.company.company', [
            'companies' => $this->companies,
        ]);
    }
}
