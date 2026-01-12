<?php

declare(strict_types=1);

namespace Src\Cargo\DomainLayer\ValueObjects;

use DateTime;

readonly class DeliveryTimeInterval
{
    public function __construct(
        public DateTime $timeFrom,
        public DateTime $timeTo,
    ) {}
}
