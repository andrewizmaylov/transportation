<?php

declare(strict_types=1);

namespace Src\Transportation\PresentationLayer\HTTP\V1\Responder;

use App\Exceptions\UnsupportedObjectException;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;
use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Responder\Contracts\ResponderInterface;
use Src\Transportation\DomainLayer\Entities\Transportation;
use Src\Transportation\DomainLayer\Enum\TransportationStatus;

class TransportationResponder implements ResponderInterface
{
    /**
     * @throws UnsupportedObjectException
     */
    public function composePaginatedResults(PaginatedResult $paginatedResults): PaginatedResult
    {
        $processedItems = array_map(fn ($record) => $this->composeEntity($record), $paginatedResults->items);

        $paginatedResults->withProcessedItems($processedItems);

        return $paginatedResults;
    }

    /**
     * @throws UnsupportedObjectException
     */
    public function composeEntity(object $entity): array
    {
        if (! $entity instanceof Transportation) {
            throw new UnsupportedObjectException;
        }

        return [
            'id' => $entity->id->value(),
            'type' => 'Transportation',
            'attributes' => [
                'id' => $entity->id->value(),
                'name' => $entity->name,
                'clientId' => $entity->clientId,
                'pickupFrom' => $entity->pickupDateInterval->from->format('Y-m-d H:i:s'),
                'pickupTo' => $entity->pickupDateInterval->to->format('Y-m-d H:i:s'),
                'transportationStatus' => $entity->transportationStatus->value,
            ],
        ];
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function composeFromModel(object $model): Transportation
    {
        return new Transportation(
            new TransportationId($model->id),
            $model->name,
            $model->client_id,
            new DateTimeInterval($model->pickup_from, $model->pickup_to),
            TransportationStatus::tryFrom($model->transportation_status),
        );
    }
}
