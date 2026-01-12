<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cargo_id' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
