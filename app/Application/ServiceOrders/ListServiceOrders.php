<?php

namespace App\Application\ServiceOrders;

use App\Application\ServiceOrders\Data\ServiceOrderListResult;
use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListServiceOrders
{
    public function handle(int $secretariatId, string $search = '', int $perPage = 15): ServiceOrderListResult
    {
        $baseQuery = ServiceOrder::query()
            ->with('category')
            ->forSecretariat($secretariatId)
            ->search($search);

        $serviceOrders = (clone $baseQuery)
            ->orderBy('is_urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $summary = [
            'total' => (clone $baseQuery)->toBase()->count(),
            'urgent' => (clone $baseQuery)->where('is_urgent', true)->toBase()->count(),
            'completed' => (clone $baseQuery)->where('status', ServiceOrderStatus::Completed->value)->toBase()->count(),
        ];

        return new ServiceOrderListResult($serviceOrders, $summary);
    }
}
