<?php

namespace App\Livewire\Admin;

use App\Models\Secretariat;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class SecretariatManager extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $description, $selected_id;
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|min:3|unique:secretariats,name',
        'description' => 'nullable|string'
    ];

    public function render()
    {
        return view('livewire.admin.secretariat-manager', [
            'secretariats' => Secretariat::where('name', 'like', '%'.$this->search.'%')
                ->withCount('categories')
                ->paginate(10)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->selected_id = null;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:secretariats,name,' . $this->selected_id,
        ]);

        Secretariat::updateOrCreate(['id' => $this->selected_id], [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description
        ]);

        session()->flash('message', $this->selected_id ? 'Secretaria atualizada!' : 'Secretaria criada com sucesso!');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $record = Secretariat::findOrFail($id);
        $this->selected_id = $id;
        $this->name = $record->name;
        $this->description = $record->description;
        $this->openModal();
    }

    public function delete($id)
    {
        Secretariat::find($id)->delete();
        session()->flash('message', 'Secretaria movida para a lixeira.');
    }
}
