<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Secretariats\Contracts\SecretariatRepository;
use App\Application\Secretariats\Data\SecretariatMutationData;
use App\Models\Secretariat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentSecretariatRepository implements SecretariatRepository
{
    public function paginate(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return Secretariat::query()
            ->search($search)
            ->withCount('categories')
            ->paginate($perPage);
    }

    public function listOptions(): Collection
    {
        return Secretariat::query()
            ->orderBy('name')
            ->get();
    }

    public function findById(int $secretariatId): ?Secretariat
    {
        return Secretariat::query()->find($secretariatId);
    }

    public function nameExists(string $name, ?int $ignoreSecretariatId = null): bool
    {
        return Secretariat::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($ignoreSecretariatId !== null, fn ($query) => $query->where('id', '!=', $ignoreSecretariatId))
            ->exists();
    }

    public function save(?Secretariat $secretariat, SecretariatMutationData $data, string $slug): Secretariat
    {
        $secretariat ??= new Secretariat;
        $secretariat->fill([
            ...$data->toPersistenceArray(),
            'slug' => $slug,
        ]);
        $secretariat->save();

        return $secretariat->refresh();
    }

    public function delete(Secretariat $secretariat): void
    {
        $secretariat->delete();
    }
}
