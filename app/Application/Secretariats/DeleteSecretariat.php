<?php

namespace App\Application\Secretariats;

use App\Application\Secretariats\Contracts\SecretariatRepository;

class DeleteSecretariat
{
    public function __construct(
        private readonly GetSecretariat $getSecretariat,
        private readonly SecretariatRepository $secretariats,
    ) {}

    public function handle(int $secretariatId): void
    {
        $this->secretariats->delete($this->getSecretariat->handle($secretariatId));
    }
}
