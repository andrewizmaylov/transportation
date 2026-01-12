<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransportationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'transportation_id' => $this->route('transportation_id'),
        ]);

        // Sanitize name field if provided
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

    public function rules(): array
    {
        return [
            'transportation_id' => ['required', 'uuid', 'exists:transportations,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'pickupFrom' => ['nullable', 'string'],
            'pickupTo' => ['nullable', 'string'],
        ];
    }
}
