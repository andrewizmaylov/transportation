<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Cargo;

use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\Cargo\ApplicationLayer\AddCargoToTransportationProcess;
use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\Cargo\PresentationLayer\HTTP\V1\Responder\CargoResponder;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\Currency;
use Src\SharedKernel\DomainLayer\ValueObjects\Money;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\AddCargoToTransportationRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/shipper/{transportation_id}/add-cargo',
    description: 'Adds cargo to delivery. Transportation identifier is passed in URI parameters',
    summary: 'Add cargo',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(
        description: 'Data for adding cargo object',
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'length', 'width', 'height', 'weight', 'price', 'currency'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Чемодан'),
                new OA\Property(property: 'length', type: 'int', example: '200'),
                new OA\Property(property: 'width', type: 'int', example: '200'),
                new OA\Property(property: 'height', type: 'int', example: '200'),
                new OA\Property(property: 'weight', type: 'int', example: '200'),
                new OA\Property(property: 'price', type: 'int', example: '65000'),
                new OA\Property(property: 'currency', type: 'string', example: 'RUB'),
            ]
        )
    ),
    tags: ['4. Shipper - Cargo'],
    parameters: [
        new OA\Parameter(
            name: 'transportation_id',
            description: 'Transportation ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Cargo created successfully',
            content: new OA\JsonContent(ref: '#/components/schemas/TransportationCard')
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error'
        ),
        new OA\Response(
            response: 500,
            description: 'Server error'
        ),
    ]
)]
readonly class AddCargoToTransportationController
{
    public function __construct(
        private LoggerInterface $logger,
        private AddCargoToTransportationProcess $addCargoToTransportationProcess,
        private CargoResponder $cargoResponder,
    ) {}

    public function __invoke(AddCargoToTransportationRequest $request): JsonResponse
    {
        $this->logger->debug('[AddCargoToTransportationController] Adding cargo to transportation.', ['request' => $request->all()]);

        try {
            $currency = $request->currency && is_string($request->currency)
                ? new Currency($request->currency)
                : new Currency('RUB');

            $cargo = $this->addCargoToTransportationProcess->execute(
                transportationId: new TransportationId($request->get('transportation_id')),
                cargoCharacteristics: new CargoCharacteristics(
                    $request->name,
                    $request->length,
                    $request->width,
                    $request->height,
                    $request->weight,
                    new Money(
                        $request->price,
                        $currency
                    )
                )
            );

            $response = new JsonResponse;
            $response->setData(
                $this->cargoResponder->composeEntity($cargo),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[AddCargoToTransportationController] An unexpected error occurred while adding cargo to transportation. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[AddCargoToTransportationController] An unexpected error occurred while adding cargo to transportation.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
