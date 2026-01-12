<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\ExceptionHandler;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\App;
use ReflectionException;

class JsonApiSpecificationMiddleware extends Middleware
{
    /**
     * @throws ReflectionException
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        App::bind(Handler::class, function () {
            return App::make(ExceptionHandler::class);
        });
        App::bind(\Illuminate\Contracts\Debug\ExceptionHandler::class, function () {
            return App::make(ExceptionHandler::class);
        });

        return $next($request);
    }
}
