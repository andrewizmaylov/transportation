<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API documentation for My Application',
    title: 'Private API for Transportation Management System',
    contact: new OA\Contact(
        name: 'API Support',
        url: 'https://example.com/support',
        email: 'andrew.izmaylov@gmail.com'
    ),
    license: new OA\License(
        name: 'Apache 2.0',
        url: 'https://www.apache.org/licenses/LICENSE-2.0.html'
    )
)]
#[OA\Server(
    url: 'https://transportation.test/public/api/v1/',
    description: 'Local development server'
)]
#[OA\Server(
    url: 'https://transportation.test/public/api/v1/',
    description: 'Production server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'apiKey',
    description: 'Enter token in format (Bearer <token>)',
    name: 'Authorization',
    in: 'header'
)]
abstract class Controller {}
