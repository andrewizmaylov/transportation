<?php

declare(strict_types=1);

namespace Src\Transportation\DomainLayer\ValueObjects;

use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;

readonly class TransportationStatus
{
    public function __construct(
        public TransportationId $status,
    ) {}
}
