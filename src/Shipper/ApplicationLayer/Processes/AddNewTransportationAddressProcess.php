<?php

declare(strict_types=1);

namespace Src\Shipper\ApplicationLayer\Processes;

use App\Exceptions\BusinessException;
use App\Services\GoogleGeocodingAPIService;
use RuntimeException;
use Src\SharedKernel\DomainLayer\Entities\City;
use Src\SharedKernel\DomainLayer\Entities\Country;
use Src\SharedKernel\DomainLayer\Entities\TransportationAddress;
use Src\SharedKernel\DomainLayer\Repository\TransportationAddressRepositoryInterface;
use Src\SharedKernel\DomainLayer\Storage\TransportationAddressStorageInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\PhoneNumber;

readonly class AddNewTransportationAddressProcess
{
    public function __construct(
        private TransportationAddressStorageInterface $storage,
        private TransportationAddressRepositoryInterface $repository,
    ) {}

    /**
     * @throws BusinessException
     */
    public function execute(
        string $alias,
        string $clientId,
        string $type,
        string $contact,
        City $city,
        string $addressLine1,
        PhoneNumber $phoneNumber,
        Country $country,
        ?string $addressLine2,
        ?string $addressLine3,
        ?string $comment,
    ): TransportationAddress {
        [$latitude, $longitude] = new GoogleGeocodingAPIService()->getCoordinatesFromAddress($country, $city, $addressLine1, $addressLine2);
        if (! $latitude || ! $longitude) {
            throw new BusinessException('Unable to determine coordinates for the given address. Check City, street, house number fields - they are required!');
        }

        $newAddressId = $this->storage->createTransportationAddress(
            alias: $alias,
            clientId: $clientId,
            type: $type,
            contact: $contact,
            cityId: $city->id,
            addressLine1: $addressLine1,
            phoneNumber: $phoneNumber->number,
            countryId: $country->id,
            latitude: $latitude,
            longitude: $longitude,
            addressLine2: $addressLine2 ?? null,
            addressLine3: $addressLine3 ?? null,
            comment: $comment ?? null,
        );

        $transportationAddress = $this->repository->findById($newAddressId);

        if ($transportationAddress === null) {
            throw new RuntimeException('TransportationAddress was not found after creation');
        }

        return $transportationAddress;
    }
}
