<?php

declare(strict_types=1);

namespace Src\Transportation\DomainLayer\Enum;

enum TransportationStatus: string
{
    case NEW = 'new';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Waiting for confirmation',
            self::PROCESSING => 'In progress',
            self::COMPLETED => 'Fulfilled',
            self::CANCELLED => 'Order cancelled',
            self::REFUNDED => 'Payment refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'gray',
            self::PROCESSING => 'blue',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
            self::REFUNDED => 'purple',
        };
    }

    public function isFinalized(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED, self::REFUNDED]);
    }
}
