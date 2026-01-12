<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportationAddress extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'city_id' => 'string',
            'country_id' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
