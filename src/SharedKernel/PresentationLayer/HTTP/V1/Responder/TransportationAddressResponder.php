<?php

declare(strict_types=1);

namespace Src\SharedKernel\PresentationLayer\HTTP\V1\Responder;

use App\Exceptions\UnsupportedObjectException;
use DateTime;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Entities\TransportationAddress;
use Src\SharedKernel\DomainLayer\Enum\TransportationAddressTypesEnum;
use Src\SharedKernel\DomainLayer\Repository\CityRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\CountryRepositoryInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Responder\Contracts\ResponderInterface;

readonly class TransportationAddressResponder implements ResponderInterface
{
    public function __construct(
        private CityRepositoryInterface $cityRepository,
        private CountryRepositoryInterface $countryRepository,
    ) {}

    /**
     * @throws UnsupportedObjectException
     */
    public function composeEntity(object $entity): array
    {
        if (! $entity instanceof TransportationAddress) {
            throw new UnsupportedObjectException;
        }

        return [
            'id' => $entity->id->value(),
            'type' => 'TransportationAddress',
            'attributes' => [
                'id' => $entity->id->value(),
                'alias' => $entity->alias,
                'clientId' => $entity->clientId,
                'type' => $entity->type->value,
                'contact' => $entity->contact,
                'city' => [
                    'id' => $entity->city->id,
                    'name' => $entity->city->name,
                ],
                'country' => [
                    'id' => $entity->country->id,
                    'iso2' => $entity->country->iso2,
                    'name' => $entity->country->name,
                ],
                'addressLine1' => $entity->addressLine1,
                'addressLine2' => $entity->addressLine2,
                'addressLine3' => $entity->addressLine3,
                'latitude' => $entity->latitude,
                'longitude' => $entity->longitude,
                'phoneNumber' => $entity->phoneNumber,
                'comment' => $entity->comment,
                'createdAt' => $entity->createdAt->format('Y-m-d H:i:s'),
                'updatedAt' => $entity->updatedAt->format('Y-m-d H:i:s'),
                'deletedAt' => $entity->deletedAt?->format('Y-m-d H:i:s'),
            ],
        ];
    }

    public function composePaginatedResults(PaginatedResult $paginatedResults): PaginatedResult
    {
        // TODO: Implement composePaginatedResults() method.
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function composeFromModel(object $model): TransportationAddress
    {
        $city = $this->cityRepository->findById($model->city_id);
        $country = $this->countryRepository->findById($model->country_id);

        return new TransportationAddress(
            id: new TransportationAddressId($model->id),
            alias: $model->alias,
            clientId: $model->client_id,
            type: TransportationAddressTypesEnum::tryFrom($model->type),
            contact: $model->contact,
            city: $city,
            addressLine1: $model->address_line_1,
            addressLine2: $model->address_line_2 ?? null,
            addressLine3: $model->address_line_3 ?? null,
            latitude: $model->latitude,
            longitude: $model->longitude,
            phoneNumber: $model->phone_number,
            comment: $model->comment ?? null,
            country: $country,
            createdAt: new DateTime($model->created_at),
            updatedAt: new DateTime($model->updated_at),
            deletedAt: $model?->deleted_at ? new DateTime($model->deleted_at) : null,
        );
    }
}
