<?php

declare(strict_types=1);

namespace Src\Transportation\DomainLayer\Storage;

use DateTimeInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\Enum\TransportationAddressTypesEnum;
use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;

interface TransportationStorageInterface
{
    public function createTransportation(
        string $name,
        DateTimeInterval $pickupDateInterval
    ): TransportationId;

    public function setTransportationAddress(
        TransportationId $transportationId,
        TransportationAddressId $id,
        TransportationAddressTypesEnum $type
    ): void;

    public function updateTransportation(
        TransportationId $transportationId,
        string $name,
        DateTimeInterface $pickupFrom,
        DateTimeInterface $pickupTo
    ): void;
}
