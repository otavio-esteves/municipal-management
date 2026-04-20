<?php

namespace App\Application\Categories;

use App\Application\Categories\Contracts\CategoryRepository;
use App\Domain\Categories\Exceptions\CategoryNotFound;
use App\Models\Category;

class GetCategory
{
    public function __construct(
        private readonly CategoryRepository $categories,
    ) {}

    public function handle(int $categoryId): Category
    {
        $category = $this->categories->findById($categoryId);

        if (! $category) {
            throw new CategoryNotFound;
        }

        return $category;
    }
}
