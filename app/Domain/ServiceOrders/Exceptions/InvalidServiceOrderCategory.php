<?php

namespace App\Domain\ServiceOrders\Exceptions;

use DomainException;

class InvalidServiceOrderCategory extends DomainException
{
    public function __construct()
    {
        parent::__construct('A categoria selecionada nao pertence a esta secretaria.');
    }
}
