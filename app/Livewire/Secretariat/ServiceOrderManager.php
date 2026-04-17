<?php

namespace App\Livewire\Secretariat;

use App\Application\ServiceOrders\CreateServiceOrder;
use App\Application\ServiceOrders\Data\ServiceOrderData;
use App\Application\ServiceOrders\Data\ServiceOrderListResult;
use App\Application\ServiceOrders\GetServiceOrder;
use App\Application\ServiceOrders\ListServiceOrders;
use App\Application\ServiceOrders\UpdateServiceOrder;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderCategory;
use App\Domain\ServiceOrders\Exceptions\ServiceOrderNotFound;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceOrderManager extends Component
{
    use AuthorizesRequests, WithPagination;

    public Secretariat $secretariat;
    public $search = '';

    // Propriedades do formulário
    public $odsId = null;
    public $title = '';
    public $location = '';
    public $categoryId = '';
    public $dueDate = '';
    public $isUrgent = false;
    public $observation = '';

    public function mount(Secretariat $secretariat)
    {
        $this->authorize('view', $secretariat);
        $this->secretariat = $secretariat;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save()
    {
        Log::info('Tentando salvar ODS...', ['title' => $this->title, 'cat' => $this->categoryId]);

        $this->validate([
            'title' => 'required|min:3',
            'categoryId' => 'required|integer',
        ]);

        try {
            $data = $this->serviceOrderData();

            if ($this->odsId) {
                $serviceOrder = app(GetServiceOrder::class)->handle($this->secretariat->id, (int) $this->odsId);
                $this->authorize('update', $serviceOrder);
                app(UpdateServiceOrder::class)->handle($this->secretariat->id, (int) $this->odsId, $data);
                session()->flash('success', 'Ordem atualizada com sucesso!');
            } else {
                $this->authorize('create', [ServiceOrder::class, $this->secretariat]);
                app(CreateServiceOrder::class)->handle($this->secretariat->id, $data);
                session()->flash('success', 'Ordem criada com sucesso!');
            }

            Log::info('ODS salva com sucesso.');

            $this->resetForm();
            $this->dispatch('ods-saved'); // Fecha o modal via Alpine

        } catch (InvalidServiceOrderCategory|ServiceOrderNotFound $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Erro ao salvar ODS: ' . $e->getMessage());
            session()->flash('error', 'Nao foi possivel salvar a ordem de servico agora. Tente novamente.');
        }
    }

    public function edit($id)
    {
        try {
            $ods = app(GetServiceOrder::class)->handle($this->secretariat->id, (int) $id);
            $this->authorize('view', $ods);
        } catch (ServiceOrderNotFound $e) {
            session()->flash('error', $e->getMessage());
            return;
        }

        $this->odsId = $ods->id;
        $this->title = $ods->title;
        $this->location = $ods->location;
        $this->categoryId = $ods->category_id;
        $this->dueDate = $ods->due_date;
        $this->isUrgent = (bool)$ods->is_urgent;
        $this->observation = $ods->observation;
        $this->dispatch('open-ods-modal', mode: 'edit');
    }

    public function resetForm()
    {
        $this->reset(['odsId', 'title', 'location', 'categoryId', 'dueDate', 'isUrgent', 'observation']);
        $this->resetValidation();
    }

    public function render()
    {
        $this->authorize('viewAny', [ServiceOrder::class, $this->secretariat]);
        /** @var ServiceOrderListResult $listing */
        $listing = app(ListServiceOrders::class)->handle($this->secretariat->id, $this->search, 15);

        return view('livewire.secretariat.service-order-manager', [
            'serviceOrders' => $listing->serviceOrders,
            'summary' => $listing->summary,
            'categories' => $this->secretariat->categories
        ])->layout('layouts.app');
    }

    private function serviceOrderData(): ServiceOrderData
    {
        return ServiceOrderData::fromArray([
            'title' => $this->title,
            'location' => $this->location,
            'category_id' => $this->categoryId,
            'due_date' => $this->dueDate,
            'is_urgent' => (bool) $this->isUrgent,
            'observation' => $this->observation,
        ]);
    }
}
