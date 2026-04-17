<?php

namespace App\Application\Secretariats;

use App\Models\Secretariat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListSecretariats
{
    public function handle(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return Secretariat::query()
            ->search($search)
            ->withCount('categories')
            ->paginate($perPage);
    }
}
