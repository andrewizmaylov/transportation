<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the table name associated with the model statically.
     */
    public static function getTableName(): string
    {
        return with(new static)->getTable();
    }

    protected $guarded = [];
}
