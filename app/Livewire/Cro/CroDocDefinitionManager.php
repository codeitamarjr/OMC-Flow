<?php

namespace App\Livewire\Cro;

use Livewire\Component;
use App\Models\CroDocDefinition;
use Illuminate\Support\Facades\Auth;

class CroDocDefinitionManager extends Component
{
    public $search = '';
    public $perPage = 10;

    public $modelId;
    public $name;
    public $code;
    public $description;
    public $days_from_ard;
    public $business;

    public $modalFormVisible = false;
    public $modalConfirmDeleteVisible = false;


    protected function rules()
    {
        $codeUnique = 'unique:cro_doc_definitions,code';
        if ($this->modelId) {
            $codeUnique .= ',' . $this->modelId;
        }

        return [
            'name'           => 'required|string|max:255',
            'code'           => ['required', 'string', 'max:50', $codeUnique],
            'description'    => 'nullable|string',
            'days_from_ard'  => 'required|integer|min:0',
        ];
    }

    public function render()
    {
        $businessId = Auth::user()->currentBusiness->id;

        $definitions = CroDocDefinition::accessible($businessId)
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%")
            )
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.cro.cro-doc-definition-manager', [
            'definitions' => $definitions,
        ]);
    }

    public function showCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->modalFormVisible = true;
    }

    public function showEditModal($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->modelId = $id;
        $record = CroDocDefinition::findOrFail($id);
        $this->name           = $record->name;
        $this->code           = $record->code;
        $this->description    = $record->description;
        $this->days_from_ard  = $record->days_from_ard;
        $this->modalFormVisible = true;
    }


    public function create()
    {

        $this->validate();

        CroDocDefinition::create([
            'name'          => $this->name,
            'code'          => $this->code,
            'description'   => $this->description,
            'days_from_ard' => $this->days_from_ard,
            'business_id'   => Auth::user()->currentBusiness->id,
        ]);

        $this->modalFormVisible = false;
        session()->flash('success', 'Definition created.');
    }


    public function update()
    {
        abort_unless(Auth::user()->ownsBusiness(CroDocDefinition::find($this->modelId)->business_id), 403);
        $this->validate();
        CroDocDefinition::find($this->modelId)->update([
            'name'           => $this->name,
            'code'           => $this->code,
            'description'    => $this->description,
            'days_from_ard'  => $this->days_from_ard,
        ]);
        $this->modalFormVisible = false;
        session()->flash('success', 'Definition updated.');
    }

    public function delete($id)
    {
        abort_unless(Auth::user()->ownsBusiness(CroDocDefinition::find($id)->business_id), 403);
        CroDocDefinition::find($id)->delete();
        session()->flash('success', 'Definition deleted.');
    }

    private function resetForm()
    {
        $this->modelId       = null;
        $this->name          = '';
        $this->code          = '';
        $this->description   = '';
        $this->days_from_ard = null;
    }
}
