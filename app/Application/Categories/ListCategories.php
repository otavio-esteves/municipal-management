<?php

namespace App\Application\Categories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCategories
{
    public function handle(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return Category::query()
            ->with('secretariat')
            ->search($search)
            ->latest()
            ->paginate($perPage);
    }
}
