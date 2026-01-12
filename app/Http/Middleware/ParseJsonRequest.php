<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParseJsonRequest
{
    /**
     * Handle an incoming request.
     * Parse JSON request body for application/json content type.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request has JSON content type
        $contentType = $request->header('Content-Type', '');

        if (str_contains($contentType, 'application/json')) {
            // Get the raw content
            $content = $request->getContent();

            if (! empty($content)) {
                $json = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                    // Merge JSON data into request
                    $request->merge($json);
                }
            }
        }

        return $next($request);
    }
}
