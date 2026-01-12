<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Src\SharedKernel\DomainLayer\Enum\TransportationAddressTypesEnum;

class AddNewTransportationAddressRequest extends FormRequest
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
    }

    public function rules(): array
    {
        return [
            'transportation_id' => 'required|uuid|exists:transportations,id',
            'alias' => 'required|string|max:255',
            'type' => ['required', Rule::enum(TransportationAddressTypesEnum::class)],
            'contact' => 'required|string',

            'city' => 'required|string',
            'addressLine1' => 'required|string',
            'addressLine2' => 'nullable|string',
            'addressLine3' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'phoneNumber' => 'required|string',
            'country' => 'nullable|string',

            'comment' => 'nullable|string',
        ];
    }
}
