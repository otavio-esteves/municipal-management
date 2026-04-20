<?php

namespace App\Application\Secretariats\Contracts;

use App\Application\Secretariats\Data\SecretariatMutationData;
use App\Models\Secretariat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SecretariatRepository
{
    public function paginate(string $search = '', int $perPage = 10): LengthAwarePaginator;

    public function listOptions(): Collection;

    public function findById(int $secretariatId): ?Secretariat;

    public function nameExists(string $name, ?int $ignoreSecretariatId = null): bool;

    public function save(?Secretariat $secretariat, SecretariatMutationData $data, string $slug): Secretariat;

    public function delete(Secretariat $secretariat): void;
}
