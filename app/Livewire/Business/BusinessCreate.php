<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class BusinessCreate extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'phone' => 'required|string|max:255',
    ];

    /**
     * Creates a new business, sets the current user as an admin, and sets the business as the user's current business.
     *
     * @return void
     */
    public function createBusiness()
    {
        $this->validate();

        $business = Business::create(['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone]);

        $business->users()->attach(Auth::id(), ['role' => 'admin']);

        Auth::user()->update(['current_business_id' => $business->id]);

        $this->reset('name');
        $this->reset('email');
        $this->reset('phone');

        $this->dispatch('business-created');
        session()->flash('success', 'Business created and set as current.');
        redirect()->route('business.index');
    }

    public function render()
    {
        return view('livewire.business.business-create');
    }
}
