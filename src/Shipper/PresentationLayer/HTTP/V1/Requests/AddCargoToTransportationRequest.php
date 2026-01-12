<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCargoToTransportationRequest extends FormRequest
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

            'name' => 'required|string|max:255',
            'length' => 'required|int|gt:0',
            'width' => 'required|int|gt:0',
            'height' => 'required|int|gt:0',
            'weight' => 'required|int|gt:0',
            'price' => 'required|int|gt:0',

            'currency' => 'nullable|string|in:EUR,USD,RUB',
        ];
    }
}
