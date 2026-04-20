<?php

namespace App\Application\ServiceOrders;

use App\Application\ServiceOrders\Contracts\ServiceOrderRepository;
use App\Domain\ServiceOrders\Exceptions\ServiceOrderNotFound;
use App\Models\ServiceOrder;

class GetServiceOrder
{
    public function __construct(
        private readonly ServiceOrderRepository $serviceOrders,
    ) {}

    public function handle(int $secretariatId, int $serviceOrderId): ServiceOrder
    {
        $serviceOrder = $this->serviceOrders->findByIdForSecretariat($secretariatId, $serviceOrderId);

        if (! $serviceOrder) {
            throw new ServiceOrderNotFound;
        }

        return $serviceOrder;
    }
}
