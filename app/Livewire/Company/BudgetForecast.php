<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyServiceContract;

class BudgetForecast extends Component
{
    use WithPagination;

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

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        return view('livewire.company.budget-forecast', [
            'companies' => $this->companies,
        ]);
    }
}
