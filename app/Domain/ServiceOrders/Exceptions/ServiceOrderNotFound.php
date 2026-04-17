<?php

namespace App\Domain\ServiceOrders\Exceptions;

use DomainException;

class ServiceOrderNotFound extends DomainException
{
    public function __construct()
    {
        parent::__construct('Ordem de servico nao encontrada para esta secretaria.');
    }
}
