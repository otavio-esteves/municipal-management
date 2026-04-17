<?php

namespace App\Application\ServiceOrders\Data;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ServiceOrderListResult
{
    /**
     * @param  array{total:int, urgent:int, completed:int}  $summary
     */
    public function __construct(
        public LengthAwarePaginator $serviceOrders,
        public array $summary,
    ) {
    }
}
