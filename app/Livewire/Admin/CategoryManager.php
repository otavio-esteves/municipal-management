<?php

namespace App\Livewire\Admin;

use App\Application\Categories\ListCategories;
use App\Models\Category;
use App\Models\Secretariat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use AuthorizesRequests, WithPagination;

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

    public function mount(): void
    {
        $this->authorize('viewAny', Category::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Category::class);

        return view('livewire.admin.category-manager', [
            'categories' => app(ListCategories::class)->handle($this->search, 10),
            'secretariats' => Secretariat::orderBy('name')->get() // Para o dropdown no formulário
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->authorize('create', Category::class);
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
        if ($this->selected_id) {
            $existingCategory = Category::findOrFail($this->selected_id);
            $this->authorize('update', $existingCategory);
        } else {
            $this->authorize('create', Category::class);
        }

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
        $this->authorize('update', $record);
        $this->selected_id = $id;
        $this->name = $record->name;
        $this->secretariat_id = $record->secretariat_id;
        $this->description = $record->description;
        $this->openModal();
    }

    public function delete($id)
    {
        $record = Category::findOrFail($id);
        $this->authorize('delete', $record);
        $record->delete(); // Aplica Soft Delete conforme o Model
        session()->flash('message', 'Categoria movida para a lixeira.');
    }
}
