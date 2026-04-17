<?php

namespace App\Livewire\Admin;

use App\Application\Secretariats\ListSecretariats;
use App\Models\Secretariat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class SecretariatManager extends Component
{
    use AuthorizesRequests, WithPagination;

    public $search = '';
    public $name, $description, $selected_id;
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|min:3|unique:secretariats,name',
        'description' => 'nullable|string'
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Secretariat::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Secretariat::class);

        return view('livewire.admin.secretariat-manager', [
            'secretariats' => app(ListSecretariats::class)->handle($this->search, 10)
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->authorize('create', Secretariat::class);
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
        if ($this->selected_id) {
            $record = Secretariat::findOrFail($this->selected_id);
            $this->authorize('update', $record);
        } else {
            $this->authorize('create', Secretariat::class);
        }

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
        $this->authorize('update', $record);
        $this->selected_id = $id;
        $this->name = $record->name;
        $this->description = $record->description;
        $this->openModal();
    }

    public function delete($id)
    {
        $record = Secretariat::findOrFail($id);
        $this->authorize('delete', $record);
        $record->delete();
        session()->flash('message', 'Secretaria movida para a lixeira.');
    }
}
