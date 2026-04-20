<?php

namespace App\Application\ServiceOrders\Contracts;

use App\Application\ServiceOrders\Data\CreateServiceOrderData;
use App\Application\ServiceOrders\Data\ServiceOrderListResult;
use App\Application\ServiceOrders\Data\UpdateServiceOrderData;
use App\Models\ServiceOrder;

interface ServiceOrderRepository
{
    public function createForSecretariat(int $secretariatId, CreateServiceOrderData $data): ServiceOrder;

    public function findByIdForSecretariat(int $secretariatId, int $serviceOrderId): ?ServiceOrder;

    public function update(ServiceOrder $serviceOrder, UpdateServiceOrderData $data): ServiceOrder;

    public function delete(ServiceOrder $serviceOrder): void;

    public function listForSecretariat(int $secretariatId, string $search = '', int $perPage = 15): ServiceOrderListResult;
}
