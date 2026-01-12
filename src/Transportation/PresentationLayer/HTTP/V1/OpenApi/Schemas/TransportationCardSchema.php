<?php

declare(strict_types=1);

namespace Src\Transportation\PresentationLayer\HTTP\V1\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransportationCard',
    required: ['id', 'type', 'attributes'],
    properties: [
        new OA\Property(
            property: 'id',
            description: 'Transportation ID',
            type: 'string',
        ),
        new OA\Property(
            property: 'type',
            type: 'string',
            example: 'Transportation',
        ),
        new OA\Property(
            property: 'attributes',
            required: [
                'id',
                'name',
                'clientId',
                'pickupFrom',
                'pickupTo',
                'transportationStatus',
            ],
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'clientId', type: 'string'),
                new OA\Property(
                    property: 'pickupFrom',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-01-01 10:00:00',
                ),
                new OA\Property(
                    property: 'pickupTo',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-01-01 18:00:00',
                ),
                new OA\Property(property: 'transportationStatus', type: 'string'),
            ],
            type: 'object',
        ),
    ],
    type: 'object',
)]
final class TransportationCardSchema {}
