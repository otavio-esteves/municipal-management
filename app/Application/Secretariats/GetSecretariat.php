<?php

namespace App\Application\Secretariats;

use App\Application\Secretariats\Contracts\SecretariatRepository;
use App\Domain\Secretariats\Exceptions\SecretariatNotFound;
use App\Models\Secretariat;

class GetSecretariat
{
    public function __construct(
        private readonly SecretariatRepository $secretariats,
    ) {}

    public function handle(int $secretariatId): Secretariat
    {
        $secretariat = $this->secretariats->findById($secretariatId);

        if (! $secretariat) {
            throw new SecretariatNotFound;
        }

        return $secretariat;
    }
}
