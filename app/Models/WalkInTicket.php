<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalkInTicket extends Model
{
    protected $fillable = [
        'ticket_number', 'booking_id', 'guest_id',
        'guest_name', 'guest_phone', 'guest_id_number',
        'valid_date', 'amount', 'status', 'qr_code', 'used_at',
    ];

    protected $casts = [
        'valid_date' => 'date',
        'used_at'    => 'datetime',
        'amount'     => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (WalkInTicket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'WIT-' . strtoupper(substr(md5(uniqid()), 0, 8));
            }
            if (empty($ticket->qr_code)) {
                $ticket->qr_code = $ticket->ticket_number;
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && $this->valid_date->isToday();
    }
}