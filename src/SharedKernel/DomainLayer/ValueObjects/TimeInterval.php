<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\ValueObjects;

use DateTime;
use InvalidArgumentException;

final readonly class TimeInterval
{
    public function __construct(
        public DateTime $timeFrom,
        public DateTime $timeTo,
    ) {
        if ($this->timeFrom > $this->timeTo) {
            throw new InvalidArgumentException('Time from cannot be greater than time to');
        }
    }
}
