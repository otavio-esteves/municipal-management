<?php

namespace App\Application\ServiceOrders;

use App\Domain\ServiceOrders\Exceptions\ServiceOrderNotFound;
use App\Models\ServiceOrder;

class GetServiceOrder
{
    public function handle(int $secretariatId, int $serviceOrderId): ServiceOrder
    {
        $serviceOrder = ServiceOrder::query()
            ->whereKey($serviceOrderId)
            ->where('secretariat_id', $secretariatId)
            ->first();

        if (! $serviceOrder) {
            throw new ServiceOrderNotFound();
        }

        return $serviceOrder;
    }
}
