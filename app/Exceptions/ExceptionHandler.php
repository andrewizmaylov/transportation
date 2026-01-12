<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Responders\Error;
use App\Responders\JsonResponse;
use App\Responders\ValidationErrorsResponder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionHandler extends Handler
{
    public function __construct(
        Container $container,
        private readonly ValidationErrorsResponder $validationErrorsResponder,
    ) {
        parent::__construct($container);
    }

    /**
     * Report or log an exception.
     *
     *
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        if ($this->shouldReport($e) && app()->bound('sentry')) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        return match (true) {
            $e instanceof AuthenticationException => new JsonResponse(
                data: [
                    'errors' => [
                        [
                            'status' => Response::HTTP_UNAUTHORIZED,
                            'code' => 'UNAUTHORIZED_ERROR',
                            'title' => Error::UNAUTHORIZED_ERROR,
                            'detail' => 'Authorization headers not found.',
                        ],
                    ],
                ],
                status: Response::HTTP_UNAUTHORIZED
            ),
            $e instanceof AuthorizationException => new JsonResponse(
                data: [
                    'errors' => [
                        [
                            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                            'code' => 'FORBIDDEN_ERROR',
                            'title' => Error::FORBIDDEN_ERROR,
                            'detail' => 'Invalid authorization data received. Access denied.',
                        ],
                    ],
                ],
                status: Response::HTTP_FORBIDDEN
            ),
            $e instanceof ValidationException => new JsonResponse(
                data: [
                    'errors' => $this->validationErrorsResponder->compose($e->errors()),
                ],
                status: Response::HTTP_UNPROCESSABLE_ENTITY
            ),

            /*$e instanceof MethodNotAllowedHttpException => new JsonResponse(status: 405),
            $e instanceof NotFoundHttpException => new JsonResponse(status: 404),
            $e instanceof ModelNotFoundException => new JsonResponse(status: 404),
            $e instanceof FileNotFoundException => new JsonResponse(status: 404),
            $e instanceof ThrottleRequestsException => new JsonResponse(status: 429),*/
            default => parent::render($request, $e)
        };
    }
}
