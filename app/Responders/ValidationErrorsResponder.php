<?php

declare(strict_types=1);

namespace App\Responders;

class ValidationErrorsResponder
{
    /**
     * Composes validation error attributes.
     *
     * @param  array  $violations  Validation errors.
     * @return array<mixed>
     */
    public function compose(array $violations): array
    {
        $errors = [];

        foreach ($violations as $key => $error) {
            $errors[] = [
                'status' => 422,
                'code' => 'BAD_REQUEST_ERROR',
                'title' => Error::BAD_REQUEST_ERROR,
                'detail' => implode(' ', $error),
                'source' => [
                    'pointer' => $key,
                ],
            ];
        }

        return $errors;
    }
}
