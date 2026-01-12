<?php

declare(strict_types=1);

namespace Src\Transportation\DomainLayer\Entities;

use Src\FinancialInformation\DomainLayer\Entities\FinancialInformation;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;
use Src\Transportation\DomainLayer\Enum\TransportationStatus;

readonly class Transportation
{
    public function __construct(
        public TransportationId $id,
        public ?string $name,
        public string $clientId,
        public DateTimeInterval $pickupDateInterval,
        public TransportationStatus $transportationStatus,
        public ?CargoId $cargoId = null,
        public ?FinancialInformation $financialInformation = null,
    ) {}
}
