<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Entities;

use DateTime;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Enum\TransportationAddressTypesEnum;

readonly class TransportationAddress
{
    public function __construct(
        public TransportationAddressId $id,
        public string $alias,
        public string $clientId,
        public TransportationAddressTypesEnum $type,
        public string $contact,
        public City $city,
        public string $addressLine1,
        public ?string $addressLine2,
        public ?string $addressLine3,
        public string $latitude,
        public string $longitude,
        public string $phoneNumber,
        public ?string $comment,
        public Country $country,
        public DateTime $createdAt,
        public DateTime $updatedAt,
        public ?DateTime $deletedAt,
    ) {}
}
