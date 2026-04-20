<?php

namespace App\Domain\Categories\Exceptions;

use RuntimeException;

class CategoryNotFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Categoria nao encontrada.');
    }
}
