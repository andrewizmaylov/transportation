<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\ValueObjects;

use DateMalformedStringException;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

class DateTimeInterval
{
    /**
     * @throws DateMalformedStringException
     */
    public function __construct(
        public DateTimeInterface|string $from {
            get {
                return $this->from;
            }
        },
        public DateTimeInterface|string $to {
            get {
                return $this->to;
            }
        },
    ) {
        $this->from = is_string($from) ? new DateTime($from) : $from;
        $this->to = is_string($to) ? new DateTime($to) : $to;

        if ($this->from > $this->to) {
            throw new InvalidArgumentException('Date from cannot be greater than date to');
        }
    }
}
