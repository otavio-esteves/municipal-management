<?php

namespace App\Livewire\Admin;

use App\Application\Secretariats\Data\CreateSecretariatData;
use App\Application\Secretariats\Data\UpdateSecretariatData;
use App\Application\Secretariats\DeleteSecretariat;
use App\Application\Secretariats\GetSecretariat;
use App\Application\Secretariats\ListSecretariats;
use App\Application\Secretariats\SaveSecretariat;
use App\Domain\Secretariats\Exceptions\SecretariatNameAlreadyExists;
use App\Domain\Secretariats\Exceptions\SecretariatNotFound;
use App\Livewire\Concerns\InteractsWithFriendlyExceptions;
use App\Models\Secretariat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class SecretariatManager extends Component
{
    use AuthorizesRequests, InteractsWithFriendlyExceptions, WithPagination;

    public $search = '';

    public $name;

    public $description;

    public $selected_id;

    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'nullable|string',
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
            'secretariats' => app(ListSecretariats::class)->handle($this->search, 10),
        ])->layout('layouts.app');
    }

    public function create(): void
    {
        $this->authorize('create', Secretariat::class);
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
        $this->selected_id = null;
    }

    public function store(): void
    {
        try {
            $secretariat = $this->selected_id ? app(GetSecretariat::class)->handle((int) $this->selected_id) : null;

            if ($secretariat) {
                $this->authorize('update', $secretariat);
            } else {
                $this->authorize('create', Secretariat::class);
            }

            $this->validate();

            app(SaveSecretariat::class)->handle(
                $this->selected_id ? (int) $this->selected_id : null,
                $this->selected_id
                    ? UpdateSecretariatData::fromArray([
                        'name' => (string) $this->name,
                        'description' => $this->description,
                    ])
                    : CreateSecretariatData::fromArray([
                        'name' => (string) $this->name,
                        'description' => $this->description,
                    ]),
            );

            session()->flash('message', $this->selected_id ? 'Secretaria atualizada!' : 'Secretaria criada com sucesso!');
            $this->closeModal();
            $this->resetInputFields();
        } catch (SecretariatNameAlreadyExists $e) {
            $this->addError('name', $e->getMessage());
        } catch (SecretariatNotFound $e) {
            $this->flashException($e);
            $this->closeModal();
            $this->resetInputFields();
        } catch (Throwable) {
            $this->flashFallback('Nao foi possivel salvar a secretaria agora.');
        }
    }

    public function edit($id): void
    {
        try {
            $record = app(GetSecretariat::class)->handle((int) $id);
            $this->authorize('update', $record);
            $this->selected_id = $record->id;
            $this->name = $record->name;
            $this->description = $record->description;
            $this->openModal();
        } catch (SecretariatNotFound $e) {
            $this->flashException($e);
        } catch (Throwable) {
            $this->flashFallback('Nao foi possivel carregar a secretaria agora.');
        }
    }

    public function delete($id): void
    {
        try {
            $record = app(GetSecretariat::class)->handle((int) $id);
            $this->authorize('delete', $record);
            app(DeleteSecretariat::class)->handle((int) $id);
            session()->flash('message', 'Secretaria movida para a lixeira.');
        } catch (SecretariatNotFound $e) {
            $this->flashException($e);
        } catch (Throwable) {
            $this->flashFallback('Nao foi possivel remover a secretaria agora.');
        }
    }
}
