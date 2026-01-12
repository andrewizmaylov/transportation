<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;
use Src\Shipper\ApplicationLayer\Actions\GetCurrentUser;
use Src\Shipper\ApplicationLayer\Processes\GetShipperTransportationListProcess;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\GetShipperTransportationsListRequest;
use Src\Transportation\PresentationLayer\HTTP\V1\Responder\TransportationResponder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/shipper/transportation-list',
    description: 'Get list of transportations for authenticated Shipper',
    summary: 'Transportation',
    security: [['sanctum' => []]],
    tags: ['2. Shipper - Transportations'],
    parameters: [
        new OA\Parameter(
            name: 'page',
            description: 'Page number',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 1)
        ),
        new OA\Parameter(
            name: 'per_page',
            description: 'Items per page',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 20, maximum: 100)
        ),
        new OA\Parameter(
            name: 'with_trashed',
            description: 'Include trashed to list?',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'bool')
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'List found successfully',
        ),
        new OA\Response(
            response: 500,
            description: 'Server error',
        ),
    ]
)]
readonly class GetShipperTransportationsListController
{
    public function __construct(
        private LoggerInterface $logger,
        private GetShipperTransportationListProcess $getTransportationListProcess,
        private TransportationResponder $transportationResponder,
    ) {}

    public function __invoke(GetShipperTransportationsListRequest $request): JsonResponse
    {
        try {
            $user = app(GetCurrentUser::class)->execute();

            $transportations = $this->getTransportationListProcess->execute(
                page: $request->page ? (int) $request->page : PaginatedResult::CURRENT_PAGE,
                perPage: $request->per_page ? (int) $request->per_page : PaginatedResult::PER_PAGE,
                filter: [
                    'clientId' => $user->userId,
                    'withTrashed' => $request->with_trashed,
                ]
            );

            $response = new JsonResponse;
            $response->setData(
                $this->transportationResponder->composePaginatedResults($transportations),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[GetShipperTransportationsListController] An unexpected error occurred while searching for transportation list. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode($exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::getErrorMessage($exception->getCode()),
                        'detail' => '[GetShipperTransportationsListController] An unexpected error occurred while searching for transportation list.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
