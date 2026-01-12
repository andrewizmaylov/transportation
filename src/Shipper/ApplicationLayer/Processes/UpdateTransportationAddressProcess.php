<?php

declare(strict_types=1);

namespace Src\Shipper\ApplicationLayer\Processes;

use App\Exceptions\BusinessException;
use App\Services\GoogleGeocodingAPIService;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\Entities\TransportationAddress;
use Src\SharedKernel\DomainLayer\Repository\TransportationAddressRepositoryInterface;
use Src\SharedKernel\DomainLayer\Storage\TransportationAddressStorageInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\PhoneNumber;

readonly class UpdateTransportationAddressProcess
{
    public function __construct(
        private TransportationAddressStorageInterface $storage,
        private TransportationAddressRepositoryInterface $repository,
    ) {}

    /**
     * @throws BusinessException
     */
    public function execute(
        TransportationId $transportationId,
        TransportationAddressId $addressId,
        ?string $alias,
        ?string $contact,
        ?string $addressLine1,
        ?string $addressLine2,
        ?string $addressLine3,
        ?PhoneNumber $phoneNumber,
        ?string $comment,
    ): TransportationAddress {
        if ($addressLine1 || $addressLine2) {
            $address = $this->repository->findById($addressId);

            [$latitude, $longitude] = new GoogleGeocodingAPIService()->getCoordinatesFromAddress(
                $address->country,
                $address->city,
                $addressLine1 ?? $address->addressLine1,
                $addressLine2 ?? $address->addressLine2,
            );
            if (! $latitude || ! $longitude) {
                throw new BusinessException('Unable to determine coordinates for the given address. Check City, street, house number fields - they are required!');
            }
        }

        $this->storage->updateTransportationAddress(
            id: $addressId,
            alias: $alias ?? null,
            contact: $contact ?? null,
            addressLine1: $addressLine1 ?? null,
            phoneNumber: $phoneNumber?->number,
            latitude: $latitude ?? null,
            longitude: $longitude ?? null,
            addressLine2: $addressLine2 ?? null,
            addressLine3: $addressLine3 ?? null,
            comment: $comment ?? null,
        );

        return $this->repository->findById($addressId);
    }
}
