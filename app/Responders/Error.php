<?php

declare(strict_types=1);

namespace App\Responders;

class Error
{
    /**
     * Status error 500.
     */
    public const INTERNAL_ERROR = 'Server error.';

    /**
     * Status error 422.
     */
    public const BAD_REQUEST_ERROR = 'Validation error.';

    /**
     * Status error 409.
     */
    public const CONFLICT_ERROR = 'Resource conflict.';

    /**
     * Status error 404.
     */
    public const NOT_FOUND_ERROR = 'Resource not found.';

    /**
     * Status error 403.
     */
    public const FORBIDDEN_ERROR = 'Access denied.';

    /**
     * Status error 401.
     */
    public const UNAUTHORIZED_ERROR = 'Unauthorized request.';

    /**
     * Status error 404.
     */
    public const RESOURCE_NOT_FOUND = 'Resource not found.';

    public static function getErrorMessage(int $status): string
    {
        return match (true) {
            $status === 500 => self::INTERNAL_ERROR,
            $status === 422 => self::BAD_REQUEST_ERROR,
            $status === 409 => self::CONFLICT_ERROR,
            $status === 404 => self::RESOURCE_NOT_FOUND,
            $status === 403 => self::FORBIDDEN_ERROR,
            $status === 401 => self::UNAUTHORIZED_ERROR,
        };
    }
}
