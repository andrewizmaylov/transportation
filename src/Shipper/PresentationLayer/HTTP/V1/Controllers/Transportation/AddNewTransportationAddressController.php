<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\Repository\CityRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\CountryRepositoryInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\PhoneNumber;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Responder\TransportationAddressResponder;
use Src\Shipper\ApplicationLayer\Processes\AddNewTransportationAddressProcess;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\AddNewTransportationAddressRequest;
use Src\Transportation\DomainLayer\Storage\TransportationStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/shipper/{transportation_id}/add-transportation-address',
    description: 'Registers pickup/delivery address for an existing transportation. Filled by cargo shipper. Transportation ID is sent in URI',
    summary: 'Add address',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(
        description: 'Data for creating address object',
        required: true,
        content: new OA\JsonContent(
            required: ['alias', 'type', 'contact', 'city', 'addressLine1', 'phoneNumber'],
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
    ],
    responses: [
        new OA\Response(
            response: 201,
            description: 'TransportationAddress created successfully',
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
readonly class AddNewTransportationAddressController
{
    public function __construct(
        private LoggerInterface $logger,
        private AddNewTransportationAddressProcess $addNewTransportationAddressProcess,
        private TransportationAddressResponder $transportationAddressResponder,
        private CityRepositoryInterface $cityRepository,
        private CountryRepositoryInterface $countryRepository,
        private TransportationStorageInterface $transportationStorage,
    ) {}

    public function __invoke(AddNewTransportationAddressRequest $request): JsonResponse
    {
        $this->logger->debug('[AddNewTransportationAddressController] Updating transportation.', ['request' => $request->all()]);

        try {
            $transportationId = new TransportationId($request->transportation_id);

            $city = $this->cityRepository->findByNative($request->city);
            $country = $this->countryRepository->findByIso2($request->country ?? 'RU');

            $address = $this->addNewTransportationAddressProcess->execute(
                alias: $request->alias,
                clientId: auth()->id(),
                type: $request->type,
                contact: $request->contact,
                city: $city,
                addressLine1: $request->addressLine1,
                phoneNumber: new PhoneNumber($request->phoneNumber),
                country: $country,
                addressLine2: $request->addressLine2 ?? null,
                addressLine3: $request->addressLine3 ?? null,
                comment: $request->comment ?? null,
            );

            $this->transportationStorage->setTransportationAddress($transportationId, $address->id, $address->type);

            $response = new JsonResponse;
            $response->setData(
                $this->transportationAddressResponder->composeEntity($address),
            );
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[AddNewTransportationAddressController] An unexpected error occurred while updating transportation. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[AddNewTransportationAddressController] An unexpected error occurred while updating transportation.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
