<?php

namespace App\Livewire\Company;

use App\Models\Company as CompanyModel;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CompanyManage extends Component
{
    public $companies;
    public ?CompanyModel $confirmingDelete = null;
    public bool $showDeleteModal = false;
    public $selectedTags = [];
    public $allTags = [];

    public function mount()
    {
        $this->loadCompanies();
        $this->loadTags();

        foreach ($this->companies as $company) {
            $this->selectedTags[$company->id] = $company->tags()->pluck('tags.id')->all();
        }
    }

    public function loadCompanies()
    {
        $this->companies = CompanyModel::where('business_id', Auth::user()->current_business_id)->get();
    }
    public function loadTags()
    {
        $this->allTags = \App\Models\Tag::where('business_id', Auth::user()->current_business_id)->get(['id', 'name']);
    }

    public function updatedSelectedTags($value, $key)
    {
        $company = CompanyModel::find($key);

        if ($company && $company->business_id === Auth::user()->current_business_id) {
            $validTagIds = collect($value)
                ->filter(fn($id) => is_numeric($id))
                ->map(fn($id) => (int) $id)
                ->toArray();

            $company->tags()->sync($validTagIds);

            session()->flash('success', 'Tags updated for company: ' . $company->name);
        }
    }

    public function updateTags($tagIds, $companyId)
    {
        $company = CompanyModel::where('id', $companyId)
            ->where('business_id', Auth::user()->current_business_id)
            ->firstOrFail();

        $company->tags()->sync($tagIds);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = CompanyModel::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if (! $this->confirmingDelete) return;

        abort_unless(
            Auth::user()->businesses()->where('business_id', $this->confirmingDelete->business_id)->exists(),
            403,
            'Unauthorized action.'
        );
        $this->confirmingDelete->delete();
        $this->confirmingDelete = null;
        $this->showDeleteModal = false;
        session()->flash('success', 'Company deleted.');
        $this->dispatch('company-deleted');
        $this->loadCompanies();
    }

    public function render()
    {
        return view('livewire.company.company-manage');
    }
}
