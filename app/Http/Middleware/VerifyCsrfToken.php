<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'public/api/*',
        'api/*',
    ];

    /**
     * Handle an incoming request.
     * Skip CSRF verification for API routes using Bearer tokens.
     */
    public function handle($request, \Closure $next)
    {
        // Skip CSRF for API routes with Bearer token authentication
        if ($this->shouldSkipCsrf($request)) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }

    /**
     * Determine if CSRF should be skipped for this request.
     */
    protected function shouldSkipCsrf($request): bool
    {
        // Skip if Bearer token is present (API request)
        $authHeader = $request->header('Authorization', '');
        if ($request->bearerToken() || str_starts_with($authHeader, 'Bearer ')) {
            return true;
        }

        // Skip if path matches API patterns
        $path = $request->path();
        $uri = $request->getRequestUri();

        if (
            str_starts_with($path, 'public/api/') ||
            str_starts_with($path, 'api/') ||
            str_contains($uri, '/api/')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the request should be excluded from CSRF verification.
     * API routes using Sanctum Bearer tokens should be excluded.
     */
    protected function inExceptArray($request)
    {
        if ($this->shouldSkipCsrf($request)) {
            return true;
        }

        return parent::inExceptArray($request);
    }
}
