<?php

namespace App\Livewire\Tag;

use App\Models\Tag;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TagCreate extends Component
{
    public $tags;
    public string $name = '';
    public $editingTagId = null;

    public function save()
    {
        $this->validate(['name' => 'required|string|max:255']);

        Tag::create([
            'business_id' => Auth::user()->current_business_id,
            'name' => $this->name,
        ]);

        $this->reset('name');
        session()->flash('success', 'Tag created.');
        redirect()->route('tag.index');
    }

    public function edit($id)
    {
        abort_if(!Auth::user()->businesses()->where('tags.id', $id)->exists(), 403, 'Unauthorized action.');
        
        $tag = Tag::findOrFail($id);
        $this->editingTagId = $id;
        $this->name = $tag->name;
    }

    public function update()
    {
        $this->validate(['name' => 'required|string|max:255']);

        $tag = Tag::where('id', $this->editingTagId)
            ->where('business_id', Auth::user()->current_business_id)
            ->firstOrFail();

        $tag->update(['name' => $this->name]);

        $this->reset(['editingTagId', 'name']);
        $this->loadTags();
        session()->flash('success', 'Tag updated.');
    }

    public function delete($id)
    {
        $tag = Tag::where('id', $id)
            ->where('business_id', Auth::user()->current_business_id)
            ->firstOrFail();

        $tag->delete();

        $this->loadTags();
        session()->flash('success', 'Tag deleted.');
    }
    public function render()
    {
        return view('livewire.tag.tag-create');
    }
}
