<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;

class Transportation extends BaseModel
{
    public function scopeNotDeleted(Builder $query): Builder
    {
        return $query->whereNull('deleted_at');
    }

    protected $casts = [
        'id' => 'string',
        'client_id' => 'string',
        'pickup_from' => 'datetime:Y-m-d H:i:s',
        'pickup_to' => 'datetime:Y-m-d H:i:s',
    ];

    public function cargos(): HasMany|self
    {
        return $this->hasMany(Cargo::class);
    }
}
