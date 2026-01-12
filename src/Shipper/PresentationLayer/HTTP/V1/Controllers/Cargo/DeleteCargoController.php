<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Cargo;

use App\Exceptions\BusinessException;
use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\Cargo\ApplicationLayer\DeleteCargoProcess;
use Src\Cargo\PresentationLayer\HTTP\V1\Responder\CargoResponder;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\DeleteCargoRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Delete(
    path: '/shipper/{transportation_id}/{cargo_id}/delete-cargo',
    description: 'Deletes cargo from delivery. Transportation and cargo identifiers are passed in URI parameters',
    summary: 'Delete cargo',
    security: [['sanctum' => []]],
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
            name: 'address_id',
            description: 'Address ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'TransportationAddress deleted successfully',
            content: new OA\JsonContent(ref: '#/components/schemas/TransportationCard')
        ),
        new OA\Response(
            response: 500,
            description: 'Server error'
        ),
    ]
)]
readonly class DeleteCargoController
{
    public function __construct(
        private LoggerInterface $logger,
        private DeleteCargoProcess $deleteCargoProcess,
        private CargoResponder $cargoResponder,
    ) {}

    public function __invoke(DeleteCargoRequest $request): JsonResponse
    {
        $this->logger->debug('[DeleteCargoController] Deleting cargo.', ['request' => $request->all()]);

        try {
            $cargo = $this->deleteCargoProcess->execute(
                transportationId: new TransportationId($request->transportation_id),
                cargoId: new CargoId($request->cargo_id),
            );

            $response = new JsonResponse;
            $response->setData(
                $this->cargoResponder->composeEntity($cargo),
            );
        } catch (BusinessException $exception) {
            $this->logger->critical('An error occurred while deleting cargo. ' . $exception->getMessage());

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setData([
                'errors' => [
                    [
                        'status' => Response::HTTP_NOT_FOUND,
                        'code' => 'CONFLICT_ERROR',
                        'title' => Error::CONFLICT_ERROR,
                        'detail' => $exception->getMessage(),
                    ],
                ],
            ]);
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[DeleteCargoController] An unexpected error occurred while deleting cargo. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[DeleteCargoController] An unexpected error occurred while deleting cargo.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
