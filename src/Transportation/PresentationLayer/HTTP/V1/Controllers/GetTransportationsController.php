<?php

declare(strict_types=1);

namespace Src\Transportation\PresentationLayer\HTTP\V1\Controllers;

use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\Transportation\InfrastructureLayer\Repository\TransportationRepository;
use Src\Transportation\PresentationLayer\HTTP\V1\Responder\TransportationResponder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/transportation',
    description: 'Returns a paginated list of all transportations',
    summary: 'Get list of all transportations',
    security: [['sanctum' => []]],
    tags: ['Transportation'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Successful operation',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/TransportationCard'),
            )
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthenticated',
        ),
    ]
)]
final readonly class GetTransportationsController
{
    public function __construct(
        private TransportationRepository $transportationRepository,
        private TransportationResponder $transportationResponder,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(): JsonResponse
    {
        try {
            $this->logger->debug('[GetTransportationsController] heated');

            $paginatedResult = $this->transportationRepository->getAllWithPagination();

            $response = new JsonResponse;
            $response->setData(
                $this->transportationResponder->composePaginatedResults($paginatedResult),
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
