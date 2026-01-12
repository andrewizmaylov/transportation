<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\ValueObjects;

use DateTime;
use InvalidArgumentException;

final readonly class DateRange
{
    /**
     * @param  string  $from  Date in Y-m-d format (e.g., "2024-01-15")
     * @param  string  $to  Date in Y-m-d format (e.g., "2024-01-20")
     * @param  TimeInterval|null  $timeIntervalFrom  Optional time interval within the date range
     */
    public function __construct(
        public string $from,
        public string $to,
        public ?TimeInterval $timeIntervalFrom = null,
        public ?TimeInterval $timeIntervalTo = null,
    ) {
        // Validate date format (Y-m-d)
        $fromDate = DateTime::createFromFormat('Y-m-d', $this->from);
        $toDate = DateTime::createFromFormat('Y-m-d', $this->to);

        if (! $fromDate || $fromDate->format('Y-m-d') !== $this->from) {
            throw new InvalidArgumentException(
                sprintf('Invalid date format for "from": %s. Expected format: Y-m-d', $this->from)
            );
        }

        if (! $toDate || $toDate->format('Y-m-d') !== $this->to) {
            throw new InvalidArgumentException(
                sprintf('Invalid date format for "to": %s. Expected format: Y-m-d', $this->to)
            );
        }

        // Validate that from <= to
        if ($fromDate > $toDate) {
            throw new InvalidArgumentException('Date "from" cannot be greater than date "to"');
        }

        // Validate time interval falls within the date range if provided
        if ($this->timeIntervalFrom !== null) {
            $intervalFromDateOnly = $this->timeIntervalFrom->timeFrom->format('Y-m-d');
            $intervalToDateOnly = $this->timeIntervalFrom->timeTo->format('Y-m-d');

            if ($intervalFromDateOnly < $this->from || $intervalToDateOnly > $this->to) {
                throw new InvalidArgumentException(
                    'Time interval must fall within the date range'
                );
            }
        }

        if ($this->timeIntervalTo !== null) {
            $intervalFromDateOnly = $this->timeIntervalTo->timeFrom->format('Y-m-d');
            $intervalToDateOnly = $this->timeIntervalTo->timeTo->format('Y-m-d');

            if ($intervalFromDateOnly < $this->from || $intervalToDateOnly > $this->to) {
                throw new InvalidArgumentException(
                    'Time interval must fall within the date range'
                );
            }
        }
    }
}
