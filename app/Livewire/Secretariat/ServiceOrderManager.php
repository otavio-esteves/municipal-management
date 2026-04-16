<?php

namespace App\Livewire\Secretariat;

use Livewire\Component;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceOrderManager extends Component
{
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
        $this->secretariat = $secretariat;
    }

    public function save()
    {
        // 1. Log para saber se o clique chegou no servidor (Verifique seu terminal!)
        Log::info('Tentando salvar ODS...', ['title' => $this->title, 'cat' => $this->categoryId]);

        $this->validate([
            'title' => 'required|min:3',
            'categoryId' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'title' => $this->title,
                'location' => $this->location,
                'category_id' => $this->categoryId,
                'due_date' => $this->dueDate ?: null,
                'is_urgent' => (bool)$this->isUrgent,
                'observation' => $this->observation,
                'secretariat_id' => $this->secretariat->id,
                'status' => 'pending',
            ];

            if ($this->odsId) {
                ServiceOrder::findOrFail($this->odsId)->update($data);
                session()->flash('success', 'Ordem atualizada com sucesso!');
            } else {
                $data['code'] = ServiceOrder::generateCode();
                ServiceOrder::create($data);
                session()->flash('success', 'Ordem criada com sucesso!');
            }

            DB::commit();
            Log::info('ODS salva com sucesso.');

            $this->resetForm();
            $this->dispatch('ods-saved'); // Fecha o modal via Alpine

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar ODS: ' . $e->getMessage());
            session()->flash('error', 'Erro técnico: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $ods = ServiceOrder::findOrFail($id);
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
        return view('livewire.secretariat.service-order-manager', [
            'serviceOrders' => $this->secretariat->serviceOrders()
                ->with('category')
                ->where('title', 'iLike', "%{$this->search}%") // Use iLike para PostgreSQL (comum no Docker do Laravel)
                ->orderBy('is_urgent', 'desc')
                ->orderBy('created_at', 'desc')
                ->get(),
            'categories' => $this->secretariat->categories
        ])->layout('layouts.app');
    }
}