<?php

namespace App\Domain\Categories\Exceptions;

use RuntimeException;

class CategorySlugAlreadyExists extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Este nome resulta em um slug ja existente em outra categoria.');
    }
}
