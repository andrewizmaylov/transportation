<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Storage;

use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;

interface TransportationAddressStorageInterface
{
    public function createTransportationAddress(
        string $alias,
        string $clientId,
        string $type,
        string $contact,
        int $cityId,
        string $addressLine1,
        string $phoneNumber,
        int $countryId,
        string $latitude,
        string $longitude,
        ?string $addressLine2,
        ?string $addressLine3,
        ?string $comment,
    ): TransportationAddressId;

    public function updateTransportationAddress(
        TransportationAddressId $id,
        ?string $alias,
        ?string $contact,
        ?string $addressLine1,
        ?string $phoneNumber,
        ?string $latitude,
        ?string $longitude,
        ?string $addressLine2,
        ?string $addressLine3,
        ?string $comment
    );
}
