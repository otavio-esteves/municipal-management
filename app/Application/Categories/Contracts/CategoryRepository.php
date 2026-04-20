<?php

namespace App\Application\Categories\Contracts;

use App\Application\Categories\Data\CategoryMutationData;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CategoryRepository
{
    public function belongsToSecretariat(int $categoryId, int $secretariatId): bool;

    public function paginate(string $search = '', int $perPage = 10): LengthAwarePaginator;

    public function findById(int $categoryId): ?Category;

    public function slugExistsForSecretariat(int $secretariatId, string $slug, ?int $ignoreCategoryId = null): bool;

    public function save(?Category $category, CategoryMutationData $data, string $slug): Category;

    public function delete(Category $category): void;
}
