<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cargo extends BaseModel
{
    public function transportation(): BelongsTo
    {
        return $this->belongsTo(Transportation::class);
    }
}
