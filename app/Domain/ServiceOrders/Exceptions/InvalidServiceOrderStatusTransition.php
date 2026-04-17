<?php

namespace App\Domain\ServiceOrders\Exceptions;

use App\Domain\ServiceOrders\ServiceOrderStatus;
use DomainException;

class InvalidServiceOrderStatusTransition extends DomainException
{
    public function __construct(ServiceOrderStatus $from, ServiceOrderStatus $to)
    {
        parent::__construct(sprintf(
            'Nao e permitido mudar o status da ordem de servico de "%s" para "%s".',
            $from->value,
            $to->value,
        ));
    }
}
