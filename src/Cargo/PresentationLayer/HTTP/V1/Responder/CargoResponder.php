<?php

declare(strict_types=1);

namespace Src\Cargo\PresentationLayer\HTTP\V1\Responder;

use DateTime;
use Src\Cargo\DomainLayer\Entities\CargoEntity;
use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\Currency;
use Src\SharedKernel\DomainLayer\ValueObjects\Money;
use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Responder\Contracts\ResponderInterface;

class CargoResponder implements ResponderInterface
{
    public function composeEntity(object $entity): array
    {
        return [
            'id' => $entity->id->value(),
            'type' => 'Cargo',
            'attributes' => [
                'id' => $entity->id->value(),
                'name' => $entity->cargoCharacteristics->name,
                'length' => $entity->cargoCharacteristics->length,
                'width' => $entity->cargoCharacteristics->width,
                'height' => $entity->cargoCharacteristics->height,
                'weight' => $entity->cargoCharacteristics->weight,
                'price' => $entity->cargoCharacteristics->price->amount,
                'currency' => $entity->cargoCharacteristics->price->currency->value(),
                'transportation_id' => $entity->transportationId?->value() ?? null,
                'client_id' => $entity->clientId,
                'deleted_at' => $entity->deletedAt ?? null,
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
    public function composeFromModel(object $model): object
    {
        return new CargoEntity(
            id: new CargoId($model->id),
            transportationId: $model->transportation_id
                ? new TransportationId($model->transportation_id)
                : null,
            clientId: $model->client_id,
            cargoCharacteristics: new CargoCharacteristics(
                name: $model->name,
                length: $model->length,
                width: $model->width,
                height: $model->height,
                weight: $model->weight,
                price: new Money(
                    amount: $model->price,
                    currency: new Currency($model->currency)
                ),
            ),
            deletedAt: $model->deleted_at ? new DateTime($model->deleted_at) : null,
        );
    }
}
