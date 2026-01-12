<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Cargo;

use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\Cargo\ApplicationLayer\UpdateCargoProcess;
use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\Cargo\PresentationLayer\HTTP\V1\Responder\CargoResponder;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\ValueObjects\Currency;
use Src\SharedKernel\DomainLayer\ValueObjects\Money;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\UpdateCargoRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Patch(
    path: '/shipper/{transportation_id}/{cargo_id}/update-cargo',
    description: 'Updates cargo data. Transportation and address identifiers are passed in URI parameters',
    summary: 'Update cargo',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(
        description: 'Data for updating cargo object',
        required: false,
        content: new OA\JsonContent(
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
        new OA\Parameter(
            name: 'cargo_id',
            description: 'Cargo ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Cargo updated successfully',
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
readonly class UpdateCargoController
{
    public function __construct(
        private LoggerInterface $logger,
        private UpdateCargoProcess $updateCargoProcess,
        private CargoResponder $cargoResponder,
    ) {}

    public function __invoke(UpdateCargoRequest $request): JsonResponse
    {
        $this->logger->debug('[UpdateCargoController] Updating cargo within transportation.', ['request' => $request->all()]);

        try {
            $currency = $request->currency && is_string($request->currency)
                ? new Currency($request->currency)
                : new Currency('RUB');

            $cargo = $this->updateCargoProcess->execute(
                new CargoId($request->cargo_id),
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
                '[UpdateCargoController] An unexpected error occurred while updating cargo. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[UpdateCargoController] An unexpected error occurred while updating cargo.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
