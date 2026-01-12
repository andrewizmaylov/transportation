<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetShipperTransportationsListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|int|gt:0',
            'per_page' => 'nullable|int|gt:0|max:100',
            'withTrashed' => 'nullable|bool',
        ];
    }
}
