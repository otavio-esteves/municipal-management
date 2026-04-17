<?php

namespace App\Application\ServiceOrders;

use App\Application\ServiceOrders\Data\ServiceOrderData;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderCategory;
use App\Models\Category;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;

class CreateServiceOrder
{
    public function handle(int $secretariatId, ServiceOrderData $data): ServiceOrder
    {
        $this->assertCategoryBelongsToSecretariat($secretariatId, $data->categoryId);

        return DB::transaction(function () use ($secretariatId, $data) {
            return ServiceOrder::create([
                ...$data->toPersistenceArray(),
                'secretariat_id' => $secretariatId,
            ])->fresh(['category']);
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
