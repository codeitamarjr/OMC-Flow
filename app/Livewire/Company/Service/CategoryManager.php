<?php

namespace App\Livewire\Company\Service;

use Livewire\Component;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryManager extends Component
{
    use AuthorizesRequests;

    public $categories;
    public string $name = '';
    public string $code = '';
    public string $description = '';

    public bool $showCreateModal = false;

    public ?ServiceCategory $confirmingDelete = null;
    public bool $showDeleteModal = false;

    public bool $showEditModal = false;
    public ?ServiceCategory $editingCategory = null;
    public string $editName = '';
    public string $editCode = '';
    public string $editDescription = '';

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->authorize('viewAny', ServiceCategory::class);
        $this->categories = ServiceCategory::where('business_id', Auth::user()->current_business_id)->get()
            ->sortBy('name');
    }

    public function openCreateModal()
    {
        $this->authorize('create', ServiceCategory::class);
        $this->reset(['name', 'code', 'description']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function create()
    {
        $this->authorize('create', ServiceCategory::class);
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:service_categories,code',
        ]);

        ServiceCategory::create([
            'business_id' => Auth::user()->current_business_id,
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'description' => $this->description,
        ]);

        $this->reset(['name', 'code', 'description']);
        $this->showCreateModal = false;
        $this->loadCategories();

        session()->flash('success', 'Service category created.');
        $this->dispatch('category-created');
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = ServiceCategory::findOrFail($id);
        $this->authorize('delete', $this->confirmingDelete);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if (! $this->confirmingDelete) return;

        abort_unless(Auth::user()->ownsBusiness($this->confirmingDelete->business_id), 403);

        $this->confirmingDelete->delete();
        $this->confirmingDelete = null;
        $this->showDeleteModal = false;
        session()->flash('success', 'Category deleted.');
        $this->dispatch('category-deleted');
        $this->loadCategories();
    }

    public function edit($id)
    {
        $this->editingCategory = ServiceCategory::findOrFail($id);

        $this->authorize('update', $this->editingCategory);
        abort_unless(Auth::user()->ownsBusiness($this->editingCategory->business_id), 403);

        $this->editName = $this->editingCategory->name;
        $this->editCode = $this->editingCategory->code;
        $this->editDescription = $this->editingCategory->description;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editCode' => 'required|string|max:50|unique:service_categories,code,' . $this->editingCategory->id,
        ]);

        if (! $this->editingCategory) return;

        $this->authorize('update', $this->editingCategory);
        abort_unless(Auth::user()->ownsBusiness($this->editingCategory->business_id), 403);

        $this->editingCategory->update([
            'name' => $this->editName,
            'code' => strtoupper($this->editCode),
            'description' => $this->editDescription,
        ]);

        $this->reset(['editingCategory', 'editName', 'editCode', 'editDescription', 'showEditModal']);
        $this->loadCategories();
        session()->flash('success', 'Category updated successfully.');
        $this->dispatch('category-updated');
    }

    public function render()
    {
        return view('livewire.company.service.category-manager');
    }
}
