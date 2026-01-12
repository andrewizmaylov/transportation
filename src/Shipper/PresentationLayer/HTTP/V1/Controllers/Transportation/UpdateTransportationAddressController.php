<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\PhoneNumber;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Responder\TransportationAddressResponder;
use Src\Shipper\ApplicationLayer\Processes\UpdateTransportationAddressProcess;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\UpdateTransportationAddressRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Patch(
    path: '/shipper/{transportation_id}/{address_id}/update-transportation-address',
    description: 'Updates pickup or delivery address. Transportation and address identifiers are passed in URI parameters',
    summary: 'Update address',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(
        description: 'Data for updating address object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'alias', type: 'string', example: 'Чемодан'),
                new OA\Property(property: 'type', type: 'string', enum: ['delivery', 'pickup'], example: 'delivery'),
                new OA\Property(property: 'contact', type: 'string', example: 'Иванов Петр Александрович'),
                new OA\Property(property: 'city', type: 'string', example: 'Москва'),
                new OA\Property(property: 'addressLine1', type: 'string', example: 'улица Строителей дом 5'),
                new OA\Property(property: 'addressLine2', type: 'string', example: 'квартира 34'),
                new OA\Property(property: 'addressLine3', type: 'string', example: 'домофон 15'),
                new OA\Property(property: 'phoneNumber', type: 'string', example: '+79789658965'),
                new OA\Property(property: 'country', type: 'string', example: 'RU'),
                new OA\Property(property: 'comment', type: 'string', example: 'Additional notes'),
            ]
        )
    ),
    tags: ['3. Shipper - Address'],
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
            description: 'TransportationAddress updated successfully',
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
readonly class UpdateTransportationAddressController
{
    public function __construct(
        private LoggerInterface $logger,
        private UpdateTransportationAddressProcess $updateTransportationAddressProcess,
        private TransportationAddressResponder $transportationAddressResponder,
    ) {}

    public function __invoke(UpdateTransportationAddressRequest $request): JsonResponse
    {
        $this->logger->debug('[UpdateTransportationAddressController] Updating transportation.', ['request' => $request->all()]);

        try {
            $transportationAddress = $this->updateTransportationAddressProcess->execute(
                transportationId: new TransportationId($request->transportation_id),
                addressId: new TransportationAddressId($request->address_id),
                alias: $request->alias ?? null,
                contact: $request->contact ?? null,
                addressLine1: $request->addressLine1 ?? null,
                addressLine2: $request->addressLine2 ?? null,
                addressLine3: $request->addressLine3 ?? null,
                phoneNumber: $request->phoneNumber ? new PhoneNumber($request->phoneNumber) : null,
                comment: $request->comment ?? null,
            );

            $response = new JsonResponse;
            $response->setData(
                $this->transportationAddressResponder->composeEntity($transportationAddress),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[UpdateTransportationAddressController] An unexpected error occurred while updating address. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[UpdateTransportationAddressController] An unexpected error occurred while updating address.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
