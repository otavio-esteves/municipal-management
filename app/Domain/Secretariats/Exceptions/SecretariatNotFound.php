<?php

namespace App\Domain\Secretariats\Exceptions;

use RuntimeException;

class SecretariatNotFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Secretaria nao encontrada.');
    }
}
