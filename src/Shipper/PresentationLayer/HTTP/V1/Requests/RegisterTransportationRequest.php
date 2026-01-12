<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterTransportationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'pickupFrom' => ['required', 'date'],
            'pickupTo' => ['required', 'date', 'after_or_equal:pickupFrom'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Sanitize the name field to prevent XSS attacks.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name') && $this->input('name') !== null) {
            $this->merge([
                'name' => $this->sanitizeName($this->input('name')),
            ]);
        }
    }

    /**
     * Sanitize the name field by stripping HTML tags.
     * This provides defense-in-depth against XSS attacks.
     * Note: Vue.js already escapes content in {{ }} templates, but this adds server-side protection.
     */
    private function sanitizeName(string $name): string
    {
        // Strip HTML/XML tags - this is sufficient for most XSS prevention
        // PHP's strip_tags() is safe and handles most cases
        $sanitized = strip_tags($name);

        return trim($sanitized);
    }
}
