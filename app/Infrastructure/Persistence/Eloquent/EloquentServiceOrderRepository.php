<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\ServiceOrders\Contracts\ServiceOrderRepository;
use App\Application\ServiceOrders\Data\CreateServiceOrderData;
use App\Application\ServiceOrders\Data\ServiceOrderListResult;
use App\Application\ServiceOrders\Data\UpdateServiceOrderData;
use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Carbon\Carbon;
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

    public function changeStatus(ServiceOrder $serviceOrder, ServiceOrderStatus $status): ServiceOrder
    {
        return DB::transaction(function () use ($serviceOrder, $status): ServiceOrder {
            $serviceOrder->changeStatus($status);

            return $serviceOrder->fresh(['category', 'checklistItems']);
        });
    }

    public function delete(ServiceOrder $serviceOrder): void
    {
        DB::transaction(function () use ($serviceOrder): void {
            $serviceOrder->delete();
        });
    }

    public function listForSecretariat(int $secretariatId, string $search = '', array $filters = [], int $perPage = 15): ServiceOrderListResult
    {
        $categoryId = isset($filters['category_id']) ? (int) $filters['category_id'] : null;
        $status = $filters['status'] ?? null;
        $urgent = $filters['urgent'] ?? null;
        $quickFilter = $filters['quick_filter'] ?? null;
        $today = Carbon::today()->toDateString();

        $baseQuery = ServiceOrder::query()
            ->with('category')
            ->forSecretariat($secretariatId)
            ->search($search)
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($status !== null && $status !== '', fn ($query) => $query->where('status', $status))
            ->when($urgent !== null, fn ($query) => $query->where('is_urgent', (bool) $urgent))
            ->when($quickFilter === 'urgent', fn ($query) => $query->where('is_urgent', true))
            ->when($quickFilter === 'in_progress', fn ($query) => $query->where('status', ServiceOrderStatus::InProgress->value))
            ->when($quickFilter === 'completed', fn ($query) => $query->where('status', ServiceOrderStatus::Completed->value))
            ->when($quickFilter === 'overdue', fn ($query) => $query
                ->whereDate('due_date', '<', $today)
                ->where('status', '!=', ServiceOrderStatus::Completed->value));

        $serviceOrders = (clone $baseQuery)
            ->orderBy('is_urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $summary = [
            'total' => (clone $baseQuery)->toBase()->count(),
            'urgent' => (clone $baseQuery)->where('is_urgent', true)->toBase()->count(),
            'overdue' => (clone $baseQuery)
                ->whereDate('due_date', '<', $today)
                ->where('status', '!=', ServiceOrderStatus::Completed->value)
                ->toBase()->count(),
            'in_progress' => (clone $baseQuery)->where('status', ServiceOrderStatus::InProgress->value)->toBase()->count(),
            'completed' => (clone $baseQuery)->where('status', ServiceOrderStatus::Completed->value)->toBase()->count(),
        ];

        return new ServiceOrderListResult($serviceOrders, $summary);
    }
}
