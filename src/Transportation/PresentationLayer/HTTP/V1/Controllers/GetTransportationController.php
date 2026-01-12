<?php

declare(strict_types=1);

namespace Src\Transportation\PresentationLayer\HTTP\V1\Controllers;

use App\Responders\Error;
use App\Responders\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\Transportation\InfrastructureLayer\Repository\TransportationRepository;
use Src\Transportation\PresentationLayer\HTTP\V1\Requests\GetTransportationRequest;
use Src\Transportation\PresentationLayer\HTTP\V1\Responder\TransportationResponder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/transportation/{transportation_id}',
    description: 'Get transportation card by ID',
    summary: 'Transportation',
    security: [['sanctum' => []]],
    tags: ['Transportation'],
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
            description: 'Card found successfully',
            content: new OA\JsonContent(ref: '#/components/schemas/TransportationCard')
        ),
        new OA\Response(
            response: 404,
            description: 'Not found',
        ),
    ]
)]
final readonly class GetTransportationController
{
    public function __construct(
        private TransportationRepository $transportationRepository,
        private TransportationResponder $transportationResponder,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(GetTransportationRequest $request): JsonResponse
    {
        try {
            $id = $request->transportation_id;
            $transportation = $this->transportationRepository->findById(new TransportationId($id));

            if ($transportation === null) {
                throw new ModelNotFoundException('Transportation with ID ' . $id . ' not found');
            }

            $response = new JsonResponse;
            $response->setData(
                $this->transportationResponder->composeEntity($transportation),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                'An unexpected error occurred while searching for transportation. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => 'An unexpected error occurred while searching for transportation.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
