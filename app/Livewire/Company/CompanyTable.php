<?php

namespace App\Livewire\Company;

use Carbon\Carbon;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Jobs\CompanyFetchCroSubmissions;

class CompanyTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortBy = 'next_annual_return';
    public $sortDirection = 'asc';
    public $allTags = [];
    public array $selectedTagFilters = [];

    public function mount()
    {
        $this->allTags = \App\Models\Tag::where('business_id', Auth::user()->current_business_id)
            ->get(['id', 'name']);

        $this->selectedTagFilters = session()->get('selected_tag_filters', []);
    }
    /**
     * A computed property that returns a paginated list of companies
     * belonging to the current business, filtered by the search term.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Company>
     */
    #[Computed]
    public function companies()
    {
        $today = Carbon::today()->toDateString();
        $soon  = Carbon::today()->addDays(56)->toDateString();

        return Company::query()
            ->where('business_id', Auth::user()->current_business_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('custom', 'like', '%' . $this->search . '%')
                        ->orWhere('company_number', 'like', '%' . $this->search . '%');
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
     * Sort the companies by the given column.
     *
     * Toggles the sort direction between ascending and descending if the
     * column is already the current sort column; otherwise, sets the sort
     * direction to ascending and updates the sort column.
     *
     * @param string $column The column to sort by.
     * @return void
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
     * Reset the pagination when the search input changes.
     *
     * This ensures that the correct page of results is displayed when the
     * search query changes.
     *
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Updates the session with the selected tag filters when the selected tag filters change.
     *
     * This is necessary because we need to store the selected tag filters in the session so
     * that the filter is preserved when the user navigates away from the page and comes back.
     *
     * @return void
     */
    public function updatingSelectedTagFilters()
    {
        session()->put('selected_tag_filters', $this->selectedTagFilters);
    }


    /**
     * Dispatches a job to update the submission documents for the specified company.
     *
     * Ensures the user has the appropriate permissions to update the company
     * before dispatching the job. Displays a success message upon completion.
     *
     * @param Company $company The company whose submission documents are to be updated.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the user does not have permission.
     */
    public function updateSubmission(Company $company)
    {
        abort_unless(Gate::allows('update', $company), 403, 'You do not have permission to update this company.');

        CompanyFetchCroSubmissions::dispatch($company);

        session()->flash('message', 'Submission documents updated successfully.');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Submission documents updated successfully.',
        ]);
    }

    public function render()
    {
        return view('livewire.company.company-table', [
            'companies' => $this->companies,
        ]);
    }
}
