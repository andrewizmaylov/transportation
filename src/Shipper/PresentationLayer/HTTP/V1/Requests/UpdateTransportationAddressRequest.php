<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransportationAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'transportation_id' => $this->route('transportation_id'),
            'address_id' => $this->route('address_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'transportation_id' => 'required|uuid|exists:transportations,id',
            'address_id' => 'required|uuid|exists:transportation_addresses,id',
            'alias' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string'],
            'addressLine1' => ['nullable', 'string'],
            'addressLine2' => ['nullable', 'string'],
            'addressLine3' => ['nullable', 'string'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
            'phoneNumber' => ['nullable', 'string'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
