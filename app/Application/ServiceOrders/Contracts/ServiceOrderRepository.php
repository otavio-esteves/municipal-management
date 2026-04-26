<?php

namespace App\Application\ServiceOrders\Contracts;

use App\Application\ServiceOrders\Data\CreateServiceOrderData;
use App\Application\ServiceOrders\Data\ServiceOrderListResult;
use App\Application\ServiceOrders\Data\UpdateServiceOrderData;
use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Models\ServiceOrder;

interface ServiceOrderRepository
{
    public function createForSecretariat(int $secretariatId, CreateServiceOrderData $data): ServiceOrder;

    public function findByIdForSecretariat(int $secretariatId, int $serviceOrderId): ?ServiceOrder;

    public function update(ServiceOrder $serviceOrder, UpdateServiceOrderData $data): ServiceOrder;

    public function changeStatus(ServiceOrder $serviceOrder, ServiceOrderStatus $status): ServiceOrder;

    public function delete(ServiceOrder $serviceOrder): void;

    /**
     * @param  array{category_id?:int|null,status?:string|null,urgent?:bool|null,quick_filter?:string|null}  $filters
     */
    public function listForSecretariat(int $secretariatId, string $search = '', array $filters = [], int $perPage = 15): ServiceOrderListResult;
}
