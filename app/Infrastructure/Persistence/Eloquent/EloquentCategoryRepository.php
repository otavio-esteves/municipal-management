<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Categories\Contracts\CategoryRepository;
use App\Application\Categories\Data\CategoryMutationData;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentCategoryRepository implements CategoryRepository
{
    public function belongsToSecretariat(int $categoryId, int $secretariatId): bool
    {
        return Category::query()
            ->whereKey($categoryId)
            ->where('secretariat_id', $secretariatId)
            ->exists();
    }

    public function paginate(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return Category::query()
            ->with('secretariat')
            ->search($search)
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $categoryId): ?Category
    {
        return Category::query()->find($categoryId);
    }

    public function slugExistsForSecretariat(int $secretariatId, string $slug, ?int $ignoreCategoryId = null): bool
    {
        return Category::query()
            ->where('secretariat_id', $secretariatId)
            ->where('slug', $slug)
            ->when($ignoreCategoryId !== null, fn ($query) => $query->where('id', '!=', $ignoreCategoryId))
            ->exists();
    }

    public function save(?Category $category, CategoryMutationData $data, string $slug): Category
    {
        $category ??= new Category;
        $category->fill([
            ...$data->toPersistenceArray(),
            'slug' => $slug,
        ]);
        $category->save();

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
