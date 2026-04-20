<?php

namespace App\Domain\Secretariats\Exceptions;

use RuntimeException;

class SecretariatNameAlreadyExists extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Ja existe uma secretaria com este nome.');
    }
}
