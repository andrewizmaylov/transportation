<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Exceptions\BusinessException;
use App\Responders\Error;
use App\Responders\JsonResponse;
use DateTime;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\RegisterTransportationRequest;
use Src\Transportation\ApplicationLayer\Processes\RegisterTransportationProcess;
use Src\Transportation\PresentationLayer\HTTP\V1\Responder\TransportationResponder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/shipper/register-transportation',
    description: 'Registers transportation request on behalf of registered user',
    summary: 'Create transportation request',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(
        description: 'Data for creating transportation object',
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'pickupFrom', 'pickupTo'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Москва - Дубай, лекарства'),
                new OA\Property(property: 'pickupFrom', type: 'string', format: 'date-time', example: '2026-01-14 12:00:00'),
                new OA\Property(property: 'pickupTo', type: 'string', format: 'date-time', example: '2026-02-14 12:00:00'),
            ]
        )
    ),
    tags: ['2. Shipper - Transportations'],
    responses: [
        new OA\Response(
            response: 201,
            description: 'Transportation created successfully',
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
readonly class RegisterTransportationController
{
    public function __construct(
        private LoggerInterface $logger,
        private RegisterTransportationProcess $registerTransportationProcess,
        private TransportationResponder $transportationResponder,
    ) {}

    public function __invoke(RegisterTransportationRequest $request): JsonResponse
    {
        $this->logger->debug('[RegisterTransportationController] Registering transportation in the system.', ['request' => $request->all()]);

        try {
            if (! auth()->check()) {
                throw new BusinessException('Can\'t create transportation without registered user.');
            }

            $pickupDateInterval = new DateTimeInterval(
                from: new DateTime($request->pickupFrom),
                to: new DateTime($request->pickupTo),
            );

            $transportation = $this->registerTransportationProcess->execute(
                name: $request->name,
                pickupDateInterval: $pickupDateInterval,
            );

            $response = new JsonResponse;
            $response->setData(
                $this->transportationResponder->composeEntity($transportation),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[RegisterTransportationController] An unexpected error occurred while registering transportation in the system. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[RegisterTransportationController] An unexpected error occurred while registering transportation in the system.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
