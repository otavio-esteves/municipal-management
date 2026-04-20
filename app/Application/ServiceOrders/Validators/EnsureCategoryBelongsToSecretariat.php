<?php

namespace App\Application\ServiceOrders\Validators;

use App\Application\Categories\Contracts\CategoryRepository;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderCategory;

class EnsureCategoryBelongsToSecretariat
{
    public function __construct(
        private readonly CategoryRepository $categories,
    ) {}

    public function handle(int $secretariatId, int $categoryId): void
    {
        if (! $this->categories->belongsToSecretariat($categoryId, $secretariatId)) {
            throw new InvalidServiceOrderCategory;
        }
    }
}
