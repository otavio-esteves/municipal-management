<?php

namespace App\Domain\ServiceOrders;

enum ServiceOrderStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::InProgress => 'Em And.',
            self::Completed => 'Concluida',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Pending, self::InProgress, self::Completed], true),
            self::InProgress => in_array($target, [self::InProgress, self::Completed], true),
            self::Completed => $target === self::Completed,
        };
    }
}
