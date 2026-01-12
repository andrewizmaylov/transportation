<?php

declare(strict_types=1);

namespace Src\SharedKernel\PresentationLayer\HTTP\V1\Controllers;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/users/check-auth',
    description: 'Check current authenticated by sanctum token user',
    summary: "Get user's details",
    security: [['sanctum' => []]],
    tags: ['1. Authentication'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Successful operation',
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthenticated'
        ),
    ],
)]
class CheckAuthenticatedUserController
{
    public function __invoke(): array
    {
        return [
            'authenticated' => auth()->check(),
            'user' => auth()->user(),
            'guard' => auth()->getDefaultDriver(),
        ];
    }
}
