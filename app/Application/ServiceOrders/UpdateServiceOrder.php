<?php

namespace App\Application\ServiceOrders;

use App\Application\ServiceOrders\Data\ServiceOrderData;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderCategory;
use App\Domain\ServiceOrders\Exceptions\ServiceOrderNotFound;
use App\Models\Category;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;

class UpdateServiceOrder
{
    public function handle(int $secretariatId, int $serviceOrderId, ServiceOrderData $data): ServiceOrder
    {
        $this->assertCategoryBelongsToSecretariat($secretariatId, $data->categoryId);

        return DB::transaction(function () use ($secretariatId, $serviceOrderId, $data) {
            $serviceOrder = ServiceOrder::query()
                ->whereKey($serviceOrderId)
                ->where('secretariat_id', $secretariatId)
                ->first();

            if (! $serviceOrder) {
                throw new ServiceOrderNotFound();
            }

            $serviceOrder->update($data->toPersistenceArray());

            return $serviceOrder->fresh(['category']);
        });
    }

    private function assertCategoryBelongsToSecretariat(int $secretariatId, int $categoryId): void
    {
        $belongsToSecretariat = Category::query()
            ->whereKey($categoryId)
            ->where('secretariat_id', $secretariatId)
            ->exists();

        if (! $belongsToSecretariat) {
            throw new InvalidServiceOrderCategory();
        }
    }
}
