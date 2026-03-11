<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'payable_type', 'payable_id', 'guest_id', 'amount', 'currency', 'method',
        'status', 'provider_reference', 'provider_response', 'notes', 'paid_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'paid_at'           => 'datetime',
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}