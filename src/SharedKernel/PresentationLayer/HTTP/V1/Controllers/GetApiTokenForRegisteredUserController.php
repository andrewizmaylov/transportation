<?php

declare(strict_types=1);

namespace Src\SharedKernel\PresentationLayer\HTTP\V1\Controllers;

use App\Responders\Error;
use App\Responders\JsonResponse;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Src\SharedKernel\ApplicationLayer\Processes\GetApiTokenForRegisteredUserProcess;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Requests\GetApiTokenForRegisteredUserRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/users/get-token',
    description: 'Get token for Registered User',
    summary: 'Obtain Sanctum token',
    requestBody: new OA\RequestBody(
        description: 'User object that needs to be created',
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
            ]
        )
    ),
    tags: ['1. Authentication'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Token created successfully',
        ),
        new OA\Response(
            response: 401,
            description: 'Authentication error'
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error'
        ),
    ],
)]
class GetApiTokenForRegisteredUserController
{
    public function __construct(
        protected LoggerInterface $logger,
        protected GetApiTokenForRegisteredUserProcess $process,
    ) {}

    public function __invoke(GetApiTokenForRegisteredUserRequest $request): JsonResponse
    {
        try {
            $tokenData = $this->process->execute($request->all());

            $response = new JsonResponse;
            $response->setData($tokenData);
        } catch (Throwable $exception) {
            $this->logger->critical(
                '[GetApiTokenForRegisteredUserController] An unexpected error occurred while retrieving token. ' . $exception->getMessage(),
                ['stacktrace' => $exception->getTraceAsString()],
            );

            $response = new JsonResponse;
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    [
                        'title' => Error::INTERNAL_ERROR,
                        'detail' => '[GetApiTokenForRegisteredUserController] An unexpected error occurred while retrieving token.',
                    ],
                ],
            ]);
        }

        return $response;
    }
}
