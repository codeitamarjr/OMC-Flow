<?php

namespace App\Livewire\Company\Service\Provider;

use Livewire\Component;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProviderManager extends Component
{
    use AuthorizesRequests;

    public $providers;
    public $categories;

    public string $name = '';
    public string $contact_name = '';
    public string $email = '';
    public string $phone = '';
    public string $website = '';
    public string $address = '';
    public string $notes = '';

    public bool $showCreateModal = false;

    public ?ServiceProvider $confirmingDelete = null;
    public bool $showDeleteModal = false;

    public bool $showEditModal = false;
    public ?ServiceProvider $editingProvider = null;
    public string $editName = '';
    public string $editContactName = '';
    public string $editEmail = '';
    public string $editPhone = '';
    public string $editWebsite = '';
    public string $editAddress = '';
    public string $editNotes = '';
    public int $category_id = 0;
    public $category_ids = [];

    public function mount()
    {
        $this->loadProviders();
        $this->categories = ServiceCategory::where('business_id', Auth::user()->current_business_id)->get();
    }

    public function loadProviders()
    {
        $this->authorize('viewAny', ServiceProvider::class);
        $this->providers = ServiceProvider::where('business_id', Auth::user()->current_business_id)->get();
    }

    public function openCreateModal()
    {
        $this->authorize('create', ServiceProvider::class);
        $this->reset(['name', 'contact_name', 'email', 'phone', 'website', 'address', 'notes']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function create()
    {
        $this->authorize('create', ServiceProvider::class);
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $provider = ServiceProvider::create([
            'business_id' => Auth::user()->current_business_id,
            'name' => $this->name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'notes' => $this->notes,
        ]);

        if (!empty($this->category_id)) {
            $provider->categories()->sync([$this->category_id]);
        }

        $this->reset(['name', 'contact_name', 'email', 'phone', 'website', 'address', 'notes']);
        $this->showCreateModal = false;
        $this->loadProviders();
        session()->flash('success', 'Service provider created.');
        $this->dispatch('provider-created');
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = ServiceProvider::findOrFail($id);
        $this->authorize('delete', $this->confirmingDelete);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if (! $this->confirmingDelete) return;

        abort_unless(Auth::user()->ownsBusiness($this->confirmingDelete->business_id), 403);
        $this->confirmingDelete->delete();
        $this->reset('confirmingDelete', 'showDeleteModal');
        $this->loadProviders();
        session()->flash('success', 'Provider deleted.');
    }

    public function edit($id)
    {
        $this->editingProvider = ServiceProvider::findOrFail($id);
        $this->authorize('update', $this->editingProvider);

        $this->editName = $this->editingProvider->name;
        $this->editContactName = $this->editingProvider->contact_name;
        $this->editEmail = $this->editingProvider->email;
        $this->editPhone = $this->editingProvider->phone;
        $this->editWebsite = $this->editingProvider->website;
        $this->editAddress = $this->editingProvider->address;
        $this->editNotes = $this->editingProvider->notes;
        $this->category_ids = $this->editingProvider->categories->pluck('id')->toArray();
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'nullable|email|max:255',
            'editPhone' => 'nullable|string|max:50',
        ]);

        if (! $this->editingProvider) return;

        $this->authorize('update', $this->editingProvider);

        $this->editingProvider->update([
            'name' => $this->editName,
            'contact_name' => $this->editContactName,
            'email' => $this->editEmail,
            'phone' => $this->editPhone,
            'website' => $this->editWebsite,
            'address' => $this->editAddress,
            'notes' => $this->editNotes,
        ]);
        $this->editingProvider->categories()->sync($this->category_ids);

        $this->reset([
            'editingProvider',
            'editName',
            'editContactName',
            'editEmail',
            'editPhone',
            'editWebsite',
            'editAddress',
            'editNotes',
            'category_ids',
            'showEditModal',
        ]);

        $this->loadProviders();
        session()->flash('success', 'Provider updated.');
    }


    public function render()
    {
        return view('livewire.company.service.provider.provider-manager');
    }
}
