<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Exceptions\BusinessException;
use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\Shipper\ApplicationLayer\Actions\GetCurrentUser;
use Src\Shipper\ApplicationLayer\Processes\GetShipperTransportationProcess;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\GetShipperTransportationByIdRequest;
use Src\Transportation\PresentationLayer\HTTP\V1\Responder\TransportationResponder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/shipper/transportation/{transportation_id}',
    description: 'Get transportation card by ID',
    summary: 'Transportation',
    security: [['sanctum' => []]],
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
            description: 'Card found successfully',
            content: new OA\JsonContent(ref: '#/components/schemas/TransportationCard')
        ),
        new OA\Response(
            response: 404,
            description: 'Not found',
        ),
    ]
)]
readonly class GetShipperTransportationByIdController
{
    public function __construct(
        private LoggerInterface $logger,
        private GetShipperTransportationProcess $getTransportationProcess,
        private TransportationResponder $transportationResponder,
    ) {}

    public function __invoke(GetShipperTransportationByIdRequest $request): JsonResponse
    {
        try {
            $user = app(GetCurrentUser::class)->execute();

            $transportation = $this->getTransportationProcess->execute(new TransportationId($request->transportation_id));

            if ($transportation->clientId !== $user->userId) {
                throw new BusinessException('Transportation does not belong to client', Response::HTTP_FORBIDDEN);
            }

            $response = new JsonResponse;
            $response->setData(
                $this->transportationResponder->composeEntity($transportation),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[GetShipperTransportationByIdController] An unexpected error occurred while searching for transportation. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode($exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::getErrorMessage($exception->getCode()),
                        'detail' => '[GetShipperTransportationByIdController] An unexpected error occurred while searching for transportation. ' . $exception->getMessage(),
                    ],
                ],
            ]);
        }

        return $response;
    }
}
