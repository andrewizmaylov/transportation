<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Responders\Error;
use App\Responders\JsonResponse;
use DateTime;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\UpdateTransportationRequest;
use Src\Transportation\ApplicationLayer\Processes\UpdateTransportationProcess;
use Src\Transportation\DomainLayer\Repository\TransportationRepositoryInterface;
use Src\Transportation\PresentationLayer\HTTP\V1\Responder\TransportationResponder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Patch(
    path: '/shipper/{transportation_id}/update-transportation',
    description: 'Updates transportation request. Transportation identifier is passed in URI parameters',
    summary: 'Update transportation request',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(
        description: 'Data for updating transportation object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Москва - Дубай, лекарства'),
                new OA\Property(property: 'pickupFrom', type: 'string', format: 'date-time', example: '2026-01-14 12:00:00'),
                new OA\Property(property: 'pickupTo', type: 'string', format: 'date-time', example: '2026-02-14 12:00:00'),
            ]
        )
    ),
    tags: ['2. Shipper - Transportations'],
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
            description: 'Transportation updated successfully',
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
readonly class UpdateTransportationController
{
    public function __construct(
        private LoggerInterface $logger,
        private UpdateTransportationProcess $updateTransportationProcess,
        private TransportationRepositoryInterface $repository,
        private TransportationResponder $transportationResponder,
    ) {}

    public function __invoke(UpdateTransportationRequest $request): JsonResponse
    {
        $this->logger->debug(
            '[UpdateTransportationController] Updating transportation.',
            ['request' => $request->all()]
        );

        try {
            $transportationId = new TransportationId($request->transportation_id);

            $transportation = $this->repository->findById($transportationId);

            $pickupDateInterval = new DateTimeInterval(
                from: $request->pickupFrom ? new DateTime($request->pickupFrom) : $transportation->pickupDateInterval->from,
                to: $request->pickupTo ? new DateTime($request->pickupTo) : $transportation->pickupDateInterval->to,
            );

            $transportation = $this->updateTransportationProcess->execute(
                transportationId: $transportationId,
                pickupDateInterval: $pickupDateInterval,
                name: $request->name,
            );

            $response = new JsonResponse;
            $response->setData(
                $this->transportationResponder->composeEntity($transportation),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[UpdateTransportationController] An unexpected error occurred while updating transportation in the system. ' . $exception->getMessage(
                ),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[UpdateTransportationController] An unexpected error occurred while updating transportation in the system.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
