<?php

namespace App\Livewire\Secretariat;

use App\Application\ServiceOrders\ChangeServiceOrderStatus;
use App\Application\ServiceOrders\CreateServiceOrder;
use App\Application\ServiceOrders\Data\CreateServiceOrderData;
use App\Application\ServiceOrders\Data\ServiceOrderListResult;
use App\Application\ServiceOrders\Data\UpdateServiceOrderData;
use App\Application\ServiceOrders\DeleteServiceOrder;
use App\Application\ServiceOrders\GetServiceOrder;
use App\Application\ServiceOrders\ListServiceOrders;
use App\Application\ServiceOrders\UpdateServiceOrder;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderCategory;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderStatusTransition;
use App\Domain\ServiceOrders\Exceptions\ServiceOrderNotFound;
use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Livewire\Actions\Logout;
use App\Livewire\Concerns\InteractsWithFriendlyExceptions;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceOrderManager extends Component
{
    use AuthorizesRequests, InteractsWithFriendlyExceptions, WithPagination;

    public Secretariat $secretariat;

    public $search = '';

    public $filterCategoryId = '';

    public $filterStatus = '';

    public $filterUrgent = '';

    public $quickFilter = '';

    // Propriedades do formulário
    public $odsId = null;

    public $title = '';

    public $location = '';

    public $categoryId = '';

    public $dueDate = '';

    public $isUrgent = false;

    public $observation = '';

    public $currentStatus = '';

    public $newChecklistItem = '';

    public array $checklistItems = [];

    public array $originalChecklistItems = [];

    public function mount(Secretariat $secretariat): void
    {
        $this->authorize('view', $secretariat);
        $this->authorize('viewAny', [ServiceOrder::class, $secretariat]);
        $this->secretariat = $secretariat;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategoryId(): void
    {
        $this->quickFilter = '';
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->quickFilter = '';
        $this->resetPage();
    }

    public function updatingFilterUrgent(): void
    {
        $this->quickFilter = '';
        $this->resetPage();
    }

    public function addChecklistItem(): void
    {
        $label = trim((string) $this->newChecklistItem);

        if ($label === '') {
            return;
        }

        $this->checklistItems[] = [
            'label' => $label,
            'is_completed' => false,
        ];

        $this->newChecklistItem = '';
    }

    public function removeChecklistItem(int $index): void
    {
        unset($this->checklistItems[$index]);
        $this->checklistItems = array_values($this->checklistItems);
    }

    public function closeModal(): void
    {
        try {
            if (trim((string) $this->newChecklistItem) !== '') {
                $this->addChecklistItem();
            }

            if ($this->shouldPersistChecklistOnClose()) {
                $this->persistChecklistChanges();
            }

            $this->resetForm();
            $this->dispatch('ods-modal-closed');
        } catch (InvalidServiceOrderCategory|ServiceOrderNotFound $e) {
            $this->flashException($e, 'error');
        } catch (\Throwable $e) {
            Log::error('Erro ao fechar modal da ODS: '.$e->getMessage());
            $this->flashFallback('Nao foi possivel salvar a checklist agora. Tente novamente.', 'error');
        }
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function updateStatus(int $id, string $status): void
    {
        try {
            $serviceOrder = $this->findServiceOrderForCurrentSecretariat($id);
            $this->authorize('update', $serviceOrder);

            $targetStatus = ServiceOrderStatus::from($status);

            app(ChangeServiceOrderStatus::class)->handle($this->secretariat->id, $id, $targetStatus);

            if ((int) $this->odsId === $id) {
                $this->edit($id, 'details');
            }

            session()->flash('success', 'Status da ordem atualizado com sucesso!');
        } catch (InvalidServiceOrderCategory|InvalidServiceOrderStatusTransition|ServiceOrderNotFound|\ValueError $e) {
            $this->flashException($e, 'error');
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar status da ODS: '.$e->getMessage());
            $this->flashFallback('Nao foi possivel atualizar o status agora. Tente novamente.', 'error');
        }
    }

    public function save(): void
    {
        Log::info('Tentando salvar ODS...', ['title' => $this->title, 'cat' => $this->categoryId]);

        $this->validate([
            'title' => 'required|min:3',
            'categoryId' => 'required|integer',
            'checklistItems.*.label' => 'nullable|string|max:255',
            'checklistItems.*.is_completed' => 'boolean',
        ]);

        try {
            if ($this->odsId) {
                $data = UpdateServiceOrderData::fromArray($this->formState());
                $serviceOrder = $this->findServiceOrderForCurrentSecretariat((int) $this->odsId);
                $this->authorize('update', $serviceOrder);
                app(UpdateServiceOrder::class)->handle($this->secretariat->id, (int) $this->odsId, $data);
                session()->flash('success', 'Ordem atualizada com sucesso!');
            } else {
                $data = CreateServiceOrderData::fromArray($this->formState());
                $this->authorize('create', [ServiceOrder::class, $this->secretariat]);
                app(CreateServiceOrder::class)->handle($this->secretariat->id, $data);
                session()->flash('success', 'Ordem criada com sucesso!');
            }

            Log::info('ODS salva com sucesso.');

            $this->resetForm();
            $this->dispatch('ods-saved'); // Fecha o modal via Alpine

        } catch (InvalidServiceOrderCategory|ServiceOrderNotFound $e) {
            $this->flashException($e, 'error');
        } catch (\Throwable $e) {
            Log::error('Erro ao salvar ODS: '.$e->getMessage());
            $this->flashFallback('Nao foi possivel salvar a ordem de servico agora. Tente novamente.', 'error');
        }
    }

    public function edit($id, string $view = 'details'): void
    {
        try {
            $ods = $this->findServiceOrderForCurrentSecretariat((int) $id);
            $this->authorize('update', $ods);
        } catch (ServiceOrderNotFound $e) {
            $this->flashException($e, 'error');

            return;
        }

        $this->fill([
            'odsId' => $ods->id,
            ...UpdateServiceOrderData::fromServiceOrder($ods)->toFormState(),
        ]);
        $this->currentStatus = $ods->status->value;
        $this->originalChecklistItems = $this->normalizeChecklistItemsForComparison($this->checklistItems);
        $this->dispatch('open-ods-modal', mode: 'edit', view: $view);
    }

    public function delete($id): void
    {
        try {
            $serviceOrder = $this->findServiceOrderForCurrentSecretariat((int) $id);
            $this->authorize('delete', $serviceOrder);
            app(DeleteServiceOrder::class)->handle($this->secretariat->id, (int) $id);

            session()->flash('success', 'Ordem removida com sucesso!');
        } catch (ServiceOrderNotFound $e) {
            $this->flashException($e, 'error');
        }
    }

    public function resetForm(): void
    {
        $this->reset(['odsId', 'title', 'location', 'categoryId', 'dueDate', 'isUrgent', 'observation', 'currentStatus', 'newChecklistItem', 'checklistItems', 'originalChecklistItems']);
        $this->resetValidation();
    }

    public function clearFilters(): void
    {
        $this->reset(['filterCategoryId', 'filterStatus', 'filterUrgent', 'quickFilter']);
        $this->resetPage();
    }

    public function applyQuickFilter(string $filter): void
    {
        $this->quickFilter = $filter === 'total' || $this->quickFilter === $filter
            ? ''
            : $filter;

        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', [ServiceOrder::class, $this->secretariat]);
        /** @var ServiceOrderListResult $listing */
        $listing = app(ListServiceOrders::class)->handle($this->secretariat->id, $this->search, $this->listFilters(), 15);

        return view('livewire.secretariat.service-order-manager', [
            'serviceOrders' => $listing->serviceOrders,
            'summary' => $listing->summary,
            'categories' => $this->secretariat->categories,
            'statusOptions' => ServiceOrderStatus::cases(),
        ])->layout('layouts.app');
    }

    /**
     * @return array{
     *     title:string,
     *     location:string,
     *     category_id:int|string,
     *     due_date:string,
     *     is_urgent:bool,
     *     observation:string,
     *     checklist_items:list<array{label?:string|null,is_completed?:bool}>
     * }
     */
    private function formState(): array
    {
        return [
            'title' => $this->title,
            'location' => $this->location,
            'category_id' => $this->categoryId,
            'due_date' => $this->dueDate,
            'is_urgent' => (bool) $this->isUrgent,
            'observation' => $this->observation,
            'checklist_items' => $this->checklistItems,
        ];
    }

    private function findServiceOrderForCurrentSecretariat(int $id): ServiceOrder
    {
        return app(GetServiceOrder::class)->handle($this->secretariat->id, $id);
    }

    /**
     * @return array{category_id?:int,status?:string,urgent?:bool,quick_filter?:string}
     */
    private function listFilters(): array
    {
        $filters = [];

        if ($this->filterCategoryId !== '') {
            $filters['category_id'] = (int) $this->filterCategoryId;
        }

        if ($this->filterStatus !== '') {
            $filters['status'] = (string) $this->filterStatus;
        }

        if ($this->filterUrgent !== '') {
            $filters['urgent'] = $this->filterUrgent === '1';
        }

        if ($this->quickFilter !== '') {
            $filters['quick_filter'] = (string) $this->quickFilter;
        }

        return $filters;
    }

    private function shouldPersistChecklistOnClose(): bool
    {
        if (! $this->odsId) {
            return false;
        }

        return $this->normalizeChecklistItemsForComparison($this->checklistItems) !== $this->originalChecklistItems;
    }

    private function persistChecklistChanges(): void
    {
        $serviceOrder = $this->findServiceOrderForCurrentSecretariat((int) $this->odsId);
        $this->authorize('update', $serviceOrder);

        $data = UpdateServiceOrderData::fromArray([
            'title' => $serviceOrder->title,
            'location' => $serviceOrder->location ?? '',
            'category_id' => $serviceOrder->category_id,
            'due_date' => $serviceOrder->due_date?->format('Y-m-d') ?? '',
            'is_urgent' => (bool) $serviceOrder->is_urgent,
            'observation' => $serviceOrder->observation ?? '',
            'checklist_items' => $this->checklistItems,
        ]);

        $updated = app(UpdateServiceOrder::class)->handle($this->secretariat->id, (int) $this->odsId, $data);

        $this->checklistItems = UpdateServiceOrderData::fromServiceOrder($updated)->toFormState()['checklistItems'];
        $this->originalChecklistItems = $this->normalizeChecklistItemsForComparison($this->checklistItems);
    }

    /**
     * @param  array<int, array{label?:string|null,is_completed?:bool}>  $items
     * @return list<array{label:string,is_completed:bool}>
     */
    private function normalizeChecklistItemsForComparison(array $items): array
    {
        $normalized = [];

        foreach (array_values($items) as $item) {
            $label = trim((string) ($item['label'] ?? ''));

            if ($label === '') {
                continue;
            }

            $normalized[] = [
                'label' => $label,
                'is_completed' => (bool) ($item['is_completed'] ?? false),
            ];
        }

        return $normalized;
    }
}
