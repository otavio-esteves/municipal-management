<?php

namespace App\Livewire\Admin;

use App\Application\Categories\Data\CreateCategoryData;
use App\Application\Categories\Data\UpdateCategoryData;
use App\Application\Categories\DeleteCategory;
use App\Application\Categories\GetCategory;
use App\Application\Categories\ListCategories;
use App\Application\Categories\SaveCategory;
use App\Application\Secretariats\ListSecretariatOptions;
use App\Domain\Categories\Exceptions\CategoryNotFound;
use App\Domain\Categories\Exceptions\CategorySlugAlreadyExists;
use App\Livewire\Concerns\InteractsWithFriendlyExceptions;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class CategoryManager extends Component
{
    use AuthorizesRequests, InteractsWithFriendlyExceptions, WithPagination;

    public $search = '';

    public $name;

    public $description;

    public $secretariat_id;

    public $selected_id;

    public $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|min:3',
            'secretariat_id' => 'required|exists:secretariats,id',
            'description' => 'nullable|string',
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
            'secretariats' => app(ListSecretariatOptions::class)->handle(),
        ])->layout('layouts.app');
    }

    public function create(): void
    {
        $this->authorize('create', Category::class);
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal(): void
    {
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetInputFields(): void
    {
        $this->name = '';
        $this->description = '';
        $this->secretariat_id = '';
        $this->selected_id = null;
    }

    public function store(): void
    {
        try {
            $category = $this->selected_id ? app(GetCategory::class)->handle((int) $this->selected_id) : null;

            if ($category) {
                $this->authorize('update', $category);
            } else {
                $this->authorize('create', Category::class);
            }

            $this->validate();

            app(SaveCategory::class)->handle(
                $this->selected_id ? (int) $this->selected_id : null,
                $this->selected_id
                    ? UpdateCategoryData::fromArray([
                        'name' => (string) $this->name,
                        'secretariat_id' => $this->secretariat_id,
                        'description' => $this->description,
                    ])
                    : CreateCategoryData::fromArray([
                        'name' => (string) $this->name,
                        'secretariat_id' => $this->secretariat_id,
                        'description' => $this->description,
                    ]),
            );

            session()->flash('message', $this->selected_id ? 'Categoria atualizada!' : 'Categoria criada com sucesso!');
            $this->closeModal();
            $this->resetInputFields();
        } catch (CategorySlugAlreadyExists $e) {
            $this->addError('name', $e->getMessage());
        } catch (CategoryNotFound $e) {
            $this->flashException($e);
            $this->closeModal();
            $this->resetInputFields();
        } catch (Throwable) {
            $this->flashFallback('Nao foi possivel salvar a categoria agora.');
        }
    }

    public function edit($id): void
    {
        try {
            $record = app(GetCategory::class)->handle((int) $id);
            $this->authorize('update', $record);
            $this->selected_id = $record->id;
            $this->name = $record->name;
            $this->secretariat_id = $record->secretariat_id;
            $this->description = $record->description;
            $this->openModal();
        } catch (CategoryNotFound $e) {
            $this->flashException($e);
        } catch (Throwable) {
            $this->flashFallback('Nao foi possivel carregar a categoria agora.');
        }
    }

    public function delete($id): void
    {
        try {
            $record = app(GetCategory::class)->handle((int) $id);
            $this->authorize('delete', $record);
            app(DeleteCategory::class)->handle((int) $id);
            session()->flash('message', 'Categoria movida para a lixeira.');
        } catch (CategoryNotFound $e) {
            $this->flashException($e);
        } catch (Throwable) {
            $this->flashFallback('Nao foi possivel remover a categoria agora.');
        }
    }
}
