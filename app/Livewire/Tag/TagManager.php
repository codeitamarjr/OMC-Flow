<?php

namespace App\Livewire\Tag;

use App\Models\Tag;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TagManager extends Component
{
    public $tags;
    public string $name = '';

    public ?Tag $confirmingDelete = null;
    public bool $showDeleteModal = false;

    public bool $showEditModal = false;
    public ?Tag $editingTag = null;
    public string $editName = '';

    public function mount()
    {
        $this->loadTags();
    }

    public function loadTags()
    {
        $this->tags = Tag::where('business_id', Auth::user()->current_business_id)->get();
    }

    /**
     * Set the tag that should be deleted and show the confirmation modal.
     *
     * @param int $id The ID of the tag to be deleted.
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->confirmingDelete = Tag::findOrFail($id);
        $this->showDeleteModal = true;
    }

    /**
     * Delete the tag that the user confirmed should be deleted.
     *
     * @return void
     */
    public function delete()
    {
        if (! $this->confirmingDelete) return;

        abort_unless(Auth::user()->ownsBusiness($this->confirmingDelete->business_id), 403);

        $this->confirmingDelete->delete();
        $this->confirmingDelete = null;
        $this->showDeleteModal = false;
        session()->flash('success', 'Tag deleted.');
        $this->dispatch('tag-deleted');
        $this->loadTags();
    }

    /**
     * Show the edit tag modal for the given tag ID.
     *
     * @param int $id The ID of the tag to be edited.
     * @return void
     */
    public function edit($id)
    {
        $this->editingTag = Tag::findOrFail($id);

        abort_unless(Auth::user()->ownsBusiness($this->editingTag->business_id), 403);

        $this->editName = $this->editingTag->name;
        $this->showEditModal = true;
    }

    /**
     * Update the tag that the user confirmed should be updated.
     *
     * @return void
     */
    public function update()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
        ]);

        if (! $this->editingTag) return;

        abort_unless(Auth::user()->ownsBusiness($this->editingTag->business_id), 403);

        $this->editingTag->update([
            'name' => $this->editName,
        ]);

        $this->reset(['editingTag', 'editName', 'showEditModal']);
        $this->loadTags();
        session()->flash('success', 'Tag updated successfully.');
        $this->dispatch('tag-updated');
    }


    public function render()
    {
        return view('livewire.tag.tag-manager');
    }
}
