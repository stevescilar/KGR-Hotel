<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Booking extends Model
{
    protected $fillable = [
        'booking_ref',
        'room_id',
        'guest_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'room_rate',
        'meal_plan',
        'subtotal',
        'tax_amount',
        'total_amount',
        'deposit_amount',
        'payment_option',
        'paid_amount',
        'payment_status',
        'status',
        'source',
        'special_requests',
        'notes',
        'checked_in_at',
        'checked_out_at',
    ];

    protected $casts = [
        'check_in'       => 'datetime',
        'check_out'      => 'datetime',
        'checked_in_at'  => 'datetime',
        'checked_out_at' => 'datetime',
        'room_rate'      => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'paid_amount'    => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payable_id')
            ->where('payable_type', self::class);
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Amount actually due now (deposit or full).
     */
    public function getAmountDueAttribute(): float
    {
        if (($this->payment_option ?? 'full') === 'deposit') {
            return (float) $this->deposit_amount;
        }
        return (float) $this->total_amount;
    }

    /**
     * Balance remaining after deposit (payable on arrival).
     */
    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function getMealPlanLabelAttribute(): string
    {
        return match($this->meal_plan ?? 'room_only') {
            'bed_breakfast' => 'Bed & Breakfast',
            'half_board'    => 'Half Board',
            'full_board'    => 'Full Board',
            default         => 'Room Only',
        };
    }

        public function orders(): HasMany
    {
        return $this->hasMany(Order::class); 
       
    }
}