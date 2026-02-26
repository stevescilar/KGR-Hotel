<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'guest_id', 'points', 'type', 'description',
        'referenceable_type', 'referenceable_id', 'balance_after',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function referenceable()
    {
        return $this->morphTo();
    }
}
