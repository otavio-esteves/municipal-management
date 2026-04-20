<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\ServiceOrders\Contracts\ServiceOrderRepository;
use App\Application\ServiceOrders\Data\CreateServiceOrderData;
use App\Application\ServiceOrders\Data\ServiceOrderListResult;
use App\Application\ServiceOrders\Data\UpdateServiceOrderData;
use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;

class EloquentServiceOrderRepository implements ServiceOrderRepository
{
    public function createForSecretariat(int $secretariatId, CreateServiceOrderData $data): ServiceOrder
    {
        return DB::transaction(function () use ($secretariatId, $data): ServiceOrder {
            $serviceOrder = ServiceOrder::create([
                ...$data->toPersistenceArray(),
                'secretariat_id' => $secretariatId,
            ]);

            if ($data->checklistItems !== []) {
                $serviceOrder->checklistItems()->createMany($data->checklistItemsForPersistence());
            }

            return $serviceOrder->fresh(['category', 'checklistItems']);
        });
    }

    public function findByIdForSecretariat(int $secretariatId, int $serviceOrderId): ?ServiceOrder
    {
        return ServiceOrder::query()
            ->with(['category', 'checklistItems'])
            ->forSecretariat($secretariatId)
            ->whereKey($serviceOrderId)
            ->first();
    }

    public function update(ServiceOrder $serviceOrder, UpdateServiceOrderData $data): ServiceOrder
    {
        return DB::transaction(function () use ($serviceOrder, $data): ServiceOrder {
            $serviceOrder->update($data->toPersistenceArray());
            $serviceOrder->checklistItems()->delete();

            if ($data->checklistItems !== []) {
                $serviceOrder->checklistItems()->createMany($data->checklistItemsForPersistence());
            }

            return $serviceOrder->fresh(['category', 'checklistItems']);
        });
    }

    public function delete(ServiceOrder $serviceOrder): void
    {
        DB::transaction(function () use ($serviceOrder): void {
            $serviceOrder->delete();
        });
    }

    public function listForSecretariat(int $secretariatId, string $search = '', int $perPage = 15): ServiceOrderListResult
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
