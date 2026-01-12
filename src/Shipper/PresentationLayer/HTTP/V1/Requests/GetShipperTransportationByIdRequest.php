<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetShipperTransportationByIdRequest extends FormRequest
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
        ];
    }
}
