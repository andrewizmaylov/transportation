<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCargoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'transportation_id' => $this->route('transportation_id'),
            'cargo_id' => $this->route('cargo_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'transportation_id' => 'required|uuid',
            'cargo_id' => 'required|uuid',

            'name' => 'nullable|string|max:255',
            'length' => 'nullable|int|gt:0',
            'width' => 'nullable|int|gt:0',
            'height' => 'nullable|int|gt:0',
            'weight' => 'nullable|int|gt:0',
            'price' => 'nullable|int|gt:0',

            'currency' => 'nullable|string|in:EUR,USD,RUB',
        ];
    }
}
