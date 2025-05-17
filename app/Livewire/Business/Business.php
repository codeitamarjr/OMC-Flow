<?php

namespace App\Livewire\Business;

use App\Models\Business as ModelsBusiness;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Business extends Component
{
    public $businesses;
    public ?ModelsBusiness $confirmingDelete = null;
    public bool $showDeleteModal = false;

    public function mount()
    {
        $this->loadBusinesses();
    }

    /**
     * Load the user's businesses from the database.
     *
     * The businesses are loaded with the pivot table data containing the role of the user in the business.
     */
    public function loadBusinesses()
    {
        $this->businesses = Auth::user()->businesses()->withPivot('role')->get();
    }

    /**
     * Switch the user's current business.
     *
     * @param int $businessId The ID of the business to switch to.
     * @return void
     */

    public function switchBusiness($businessId)
    {
        $user = Auth::user();

        abort_if(!$user->businesses()->where('business_id', $businessId)->exists(), 403, 'Unauthorized action.');

        $user->current_business_id = $businessId;
        $user->save();

        session()->flash('success', 'Business switched.');
        $this->dispatch('business-switched');
    }

    /**
     * Confirm the deletion of a business.
     *
     * @param int $id The ID of the business to be deleted.
     * @return void
     */

    public function confirmDelete($id)
    {
        $this->confirmingDelete = ModelsBusiness::findOrFail($id);
        $this->showDeleteModal = true;
    }

    /**
     * Delete the business.
     *
     * @return void
     */
    public function delete()
    {
        if (! $this->confirmingDelete) return;

        abort_unless(
            Auth::user()->businesses()->where('business_id', $this->confirmingDelete->id)->exists(),
            403,
            'Unauthorized action.'
        );
        abort_unless(
            Auth::user()->roleInBusiness($this->confirmingDelete->id) === 'admin',
            403,
            'Unauthorized action.'
        );
        $this->confirmingDelete->delete();
        $this->confirmingDelete = null;
        $this->showDeleteModal = false;
        session()->flash('success', 'Business deleted.');
        $this->dispatch('business-deleted');
        $this->loadBusinesses();
    }

    public function render()
    {
        return view('livewire.business.business');
    }
}
