<?php

namespace App\Application\Categories;

use App\Application\Categories\Contracts\CategoryRepository;

class DeleteCategory
{
    public function __construct(
        private readonly GetCategory $getCategory,
        private readonly CategoryRepository $categories,
    ) {}

    public function handle(int $categoryId): void
    {
        $this->categories->delete($this->getCategory->handle($categoryId));
    }
}
