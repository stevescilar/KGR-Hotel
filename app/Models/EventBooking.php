<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'event_package_id', 'event_type',
        'contact_name', 'contact_email', 'contact_phone',
        'event_date', 'start_time', 'end_time', 'guest_count',
        'quoted_amount', 'deposit_amount', 'deposit_paid',
        'status', 'requirements', 'notes',
    ];

    protected $casts = ['event_date' => 'date'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (EventBooking $eb) {
            $eb->reference = 'EVT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
        });
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(EventPackage::class);
    }
}
