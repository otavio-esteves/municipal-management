<?php

namespace App\Application\ServiceOrders;

use App\Application\ServiceOrders\Contracts\ServiceOrderRepository;

class DeleteServiceOrder
{
    public function __construct(
        private readonly GetServiceOrder $getServiceOrder,
        private readonly ServiceOrderRepository $serviceOrders,
    ) {}

    public function handle(int $secretariatId, int $serviceOrderId): void
    {
        $serviceOrder = $this->getServiceOrder->handle($secretariatId, $serviceOrderId);

        $this->serviceOrders->delete($serviceOrder);
    }
}
