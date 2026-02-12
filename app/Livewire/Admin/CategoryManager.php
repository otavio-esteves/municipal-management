<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Secretariat;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $description, $secretariat_id, $selected_id;
    public $isModalOpen = false;

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'secretariat_id' => 'required|exists:secretariats,id',
            'description' => 'nullable|string'
        ];
    }

    public function render()
    {
        return view('livewire.admin.category-manager', [
            'categories' => Category::with('secretariat') // Eager Loading para performance
                ->where('name', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
            'secretariats' => Secretariat::orderBy('name')->get() // Para o dropdown no formulário
        ])->layout('layouts.app');
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
        $this->secretariat_id = '';
        $this->selected_id = null;
    }

    public function store()
    {
        $this->validate();

        $slug = Str::slug($this->name);

        $existing = Category::where('secretariat_id', $this->secretariat_id)
            ->where('slug', $slug)
            ->where('id', '!=', $this->selected_id)
            ->first();

        if ($existing) {
            $this->addError('name', 'Este nome resulta em um slug já existente em outra categoria.');
            return;
        }

        Category::updateOrCreate(['id' => $this->selected_id], [
            'secretariat_id' => $this->secretariat_id,
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description
        ]);

        session()->flash('message', $this->selected_id ? 'Categoria atualizada!' : 'Categoria criada com sucesso!');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $record = Category::findOrFail($id);
        $this->selected_id = $id;
        $this->name = $record->name;
        $this->secretariat_id = $record->secretariat_id;
        $this->description = $record->description;
        $this->openModal();
    }

    public function delete($id)
    {
        Category::find($id)->delete(); // Aplica Soft Delete conforme o Model
        session()->flash('message', 'Categoria movida para a lixeira.');
    }
}