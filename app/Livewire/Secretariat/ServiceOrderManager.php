<?php

namespace App\Livewire\Secretariat;

use Livewire\Component;
use App\Models\Secretariat;
// use App\Models\ServiceOrder; // Descomente quando o model for criado
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ServiceOrderManager extends Component
{
    public Secretariat $secretariat;

    // Propriedades para o Modal de Criação/Edição ligadas via wire:model
    public $odsId = null;
    public $title = '';
    public $location = '';
    public $categoryId = '';
    public $dueDate = '';
    public $isUrgent = false;
    public $observation = '';
    
    // Na fase 3, o checklist será gerenciado aqui ou em um componente filho
    public $checklist = [];

    public function mount(Secretariat $secretariat)
    {
        $this->secretariat = $secretariat;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'categoryId' => 'required',
        ]);

        // Lógica de Create ou Update (adaptar para seu ServiceOrder)
        // ServiceOrder::updateOrCreate(['id' => $this->odsId], [...]);

        $this->resetForm();
        $this->dispatch('ods-saved'); // Dispara evento para fechar o modal via Alpine
    }

    public function resetForm()
    {
        $this->reset(['odsId', 'title', 'location', 'categoryId', 'dueDate', 'isUrgent', 'observation', 'checklist']);
    }

    public function render()
    {
        // Aqui você buscará do banco. Mockado para renderizar o design inicial:
        $serviceOrders = [
            (object)['id' => '001', 'code' => 'ODS-001', 'title' => 'Troca de Lâmpada', 'location' => 'Av. Bernardo Sayão', 'category' => 'Iluminação', 'date' => '25/11/2025', 'urgent' => true, 'status' => 'pending', 'color' => 'red'],
            (object)['id' => '045', 'code' => 'ODS-045', 'title' => 'Roçagem Praça', 'location' => 'Praça da Bíblia', 'category' => 'Limpeza', 'date' => '20/11/2025', 'urgent' => false, 'status' => 'in_progress', 'color' => 'blue'],
            (object)['id' => '010', 'code' => 'ODS-010', 'title' => 'Troca Relé', 'location' => 'Av. Brasil', 'category' => 'Iluminação', 'date' => '10/11/2025', 'urgent' => false, 'status' => 'completed', 'color' => 'emerald'],
        ];

        return view('livewire.secretariat.service-order-manager', [
            'serviceOrders' => $serviceOrders
        ]);
    }
}