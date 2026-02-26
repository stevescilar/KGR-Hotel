<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphMany};

class GateTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'qr_code', 'ticket_type_id', 'guest_id',
        'guest_name', 'guest_phone', 'guest_email',
        'visit_date', 'quantity', 'unit_price', 'total_price',
        'status', 'scanned_at', 'scanned_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'scanned_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (GateTicket $ticket) {
            $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
            $ticket->qr_code = base64_encode(json_encode([
                'id'   => $ticket->ticket_number,
                'date' => $ticket->visit_date,
                'qty'  => $ticket->quantity,
            ]));
        });
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
