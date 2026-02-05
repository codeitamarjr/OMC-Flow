<?php

namespace App\Livewire\Company;

use App\Jobs\CompanyFetchCroSubmissions;
use App\Models\Business;
use App\Models\Company as ModelsCompany;
use App\Services\Core\CroSearchService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Company extends Component
{
    use WithPagination;

    /**
     * @var array<int, string>
     */
    public array $coreCroCodes = ['B1', 'B10'];

    public $search = '';
    public $perPage = 20;
    public $sortBy = 'next_annual_return';
    public $sortDirection = 'asc';
    public string $obligationFilter = 'all';
    public $allTags = [];
    public array $selectedTagFilters = [];
    public ?ModelsCompany $selectedCompany = null;
    public bool $showDetailsModal = false;
    public ?array $croDefinitions = null;
    public bool $showCroDefinitionsModal = false;
    public array $financialYearEnds = [];
    public array $lastAGMs = [];
    public $active = true;

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
        $query = Business::find(Auth::user()->currentBusiness->id)->companies()
            ->with([
                'tags:id,name',
                'croDocDefinitions',
            ])
            ->addSelect([
                'nearest_deadline' => DB::table('company_cro_document')
                    ->join('cro_doc_definitions', 'cro_doc_definitions.id', '=', 'company_cro_document.cro_doc_definition_id')
                    ->selectRaw('MIN(due_date)')
                    ->whereColumn('company_cro_document.company_id', 'companies.id')
                    ->whereNotNull('due_date')
                    ->whereIn('cro_doc_definitions.code', $this->coreCroCodes),
                'max_risk_score' => DB::table('company_cro_document')
                    ->join('cro_doc_definitions', 'cro_doc_definitions.id', '=', 'company_cro_document.cro_doc_definition_id')
                    ->selectRaw("
                        MAX(
                            CASE status
                                WHEN 'overdue' THEN 4
                                WHEN 'risky' THEN 3
                                WHEN 'missing' THEN 2
                                WHEN 'due_soon' THEN 1
                                ELSE 0
                            END
                        )
                    ")
                    ->whereColumn('company_cro_document.company_id', 'companies.id')
                    ->whereIn('cro_doc_definitions.code', $this->coreCroCodes),
            ])
            ->when($this->active, function ($query) {
                $query->where(function ($q) {
                    $q->where('active', true)
                        ->orWhereNull('active');
                });
            }, function ($query) {
                $query->where('active', false);
            })
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
            ->when($this->obligationFilter !== 'all', function ($query) {
                $query->whereHas('croDocDefinitions', function ($q) {
                    $q->whereIn('cro_doc_definitions.code', $this->coreCroCodes);

                    if ($this->obligationFilter === 'issues') {
                        $q->whereIn('company_cro_document.status', ['missing', 'overdue', 'risky']);
                        return;
                    }

                    $q->where('company_cro_document.status', $this->obligationFilter);
                });
            });

        if ($this->sortBy === 'nearest_deadline') {
            $query->orderByRaw(
                'CASE WHEN nearest_deadline IS NULL THEN 1 ELSE 0 END, nearest_deadline ' . $this->sortDirection
            );
        } elseif ($this->sortBy === 'max_risk_score') {
            $query->orderBy('max_risk_score', $this->sortDirection)
                ->orderByRaw('CASE WHEN nearest_deadline IS NULL THEN 1 ELSE 0 END, nearest_deadline asc');
        } else {
            $query->orderBy('companies.' . $this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    public function showAllCompanies()
    {
        $this->active = !$this->active;
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
            $this->sortDirection = $column === 'max_risk_score' ? 'desc' : 'asc';
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
        $this->resetPage();
        session()->put('selected_tag_filters', $this->selectedTagFilters);
    }

    public function updatedObligationFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedTagFilters = [];
        $this->obligationFilter = 'all';
        $this->resetPage();

        session()->forget('selected_tag_filters');
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

    public function refreshCompanyFromCro(int $companyId): void
    {
        $company = ModelsCompany::findOrFail($companyId);

        abort_unless(
            Auth::user()->businesses()->where('business_id', $company->business_id)->exists(),
            403,
            'You do not have permission to update this company'
        );

        if (!$this->isValidCompanyNumber($company->company_number)) {
            session()->flash('error', 'This company does not have a valid CRO number to refresh.');
            return;
        }

        try {
            /** @var CroSearchService $cro */
            $cro = app(CroSearchService::class);
            $details = $cro->getCompanyDetails($company->company_number);

            if (empty($details)) {
                session()->flash('error', 'CRO returned no details for that company number.');
                return;
            }

            $company->update($this->mapCroDetails($details));
            $company->refresh();

            $this->financialYearEnds[$company->id] = $company->financial_year_end;
            $this->lastAGMs[$company->id] = $company->last_agm;

            session()->flash('success', 'Company refreshed from the CRO.');
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'CRO API error: ' . $e->getMessage());
        }
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

    protected function mapCroDetails(array $details): array
    {
        return [
            'name' => $this->uppercase($details['company_name'] ?? null),
            'company_type' => $details['comp_type_desc'] ?? null,
            'status' => $details['company_status_desc'] ?? null,
            'effective_date' => $this->formatCroDate($details['company_status_date'] ?? null),
            'registration_date' => $this->formatCroDate($details['company_reg_date'] ?? null),
            'last_annual_return' => $this->formatCroDate($details['last_ar_date'] ?? null),
            'next_annual_return' => $this->formatCroDate($details['next_ar_date'] ?? null),
            'next_financial_statement_due' => $this->formatCroDate($details['next_fs_due_date'] ?? null),
            'last_accounts' => $this->formatCroDate($details['last_acc_date'] ?? null),
            'last_agm' => $this->formatCroDate($details['last_agm_date'] ?? null),
            'financial_year_end' => $this->formatCroDate($details['financial_year_end'] ?? null),
            'postcode' => $this->uppercase($details['eircode'] ?? null),
            'address_line_1' => $this->uppercase($details['company_addr_1'] ?? null),
            'address_line_2' => $this->uppercase($details['company_addr_2'] ?? null),
            'address_line_3' => $this->uppercase($details['company_addr_3'] ?? null),
            'address_line_4' => $this->uppercase($details['company_addr_4'] ?? null),
            'place_of_business' => $this->uppercase($details['place_of_business'] ?? null),
            'company_type_code' => $details['company_type_code'] ?? null,
            'company_status_code' => $details['company_status_code'] ?? null,
        ];
    }

    protected function formatCroDate(?string $isoDate): ?string
    {
        if (!$isoDate || $isoDate === '0001-01-01T00:00:00Z') {
            return null;
        }

        try {
            return Carbon::parse($isoDate)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    protected function uppercase(?string $value): ?string
    {
        return $value === null ? null : strtoupper($value);
    }

    protected function isValidCompanyNumber(?string $number): bool
    {
        if ($number === null) {
            return false;
        }

        return (bool) preg_match('/^\d{5,6}$/', $number);
    }

    public function render()
    {
        return view('livewire.company.company', [
            'companies' => $this->companies,
        ]);
    }
}
