<?php

namespace App\Application\Secretariats;

use App\Application\Secretariats\Contracts\SecretariatRepository;
use App\Application\Secretariats\Data\SecretariatMutationData;
use App\Domain\Secretariats\Exceptions\SecretariatNameAlreadyExists;
use App\Models\Secretariat;
use Illuminate\Support\Str;

class SaveSecretariat
{
    public function __construct(
        private readonly SecretariatRepository $secretariats,
        private readonly GetSecretariat $getSecretariat,
    ) {}

    public function handle(?int $secretariatId, SecretariatMutationData $data): Secretariat
    {
        $secretariat = $secretariatId === null ? null : $this->getSecretariat->handle($secretariatId);

        if ($this->secretariats->nameExists($data->name, $secretariat?->id)) {
            throw new SecretariatNameAlreadyExists;
        }

        return $this->secretariats->save($secretariat, $data, Str::slug($data->name));
    }
}
