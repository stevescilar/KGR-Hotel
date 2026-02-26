<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'table_id', 'guest_id', 'guest_name', 'guest_phone',
        'party_size', 'reserved_at', 'duration_minutes', 'status', 'notes',
    ];

    protected $casts = ['reserved_at' => 'datetime'];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
