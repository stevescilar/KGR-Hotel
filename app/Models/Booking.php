<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_ref', 'room_id', 'guest_id', 'user_id',
        'check_in', 'check_out', 'adults', 'children',
        'room_rate', 'subtotal', 'tax_amount', 'discount_amount',
        'total_amount', 'paid_amount', 'status', 'payment_status',
        'source', 'special_requests', 'internal_notes',
        'confirmed_at', 'checked_in_at', 'checked_out_at', 'cancelled_at',
    ];

    protected $casts = [
        'check_in'       => 'date',
        'check_out'      => 'date',
        'confirmed_at'   => 'datetime',
        'checked_in_at'  => 'datetime',
        'checked_out_at' => 'datetime',
        'cancelled_at'   => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Booking $booking) {
            $booking->booking_ref = 'KGR-' . date('Y') . '-' . str_pad(
                static::withTrashed()->count() + 1,
                5, '0', STR_PAD_LEFT
            );
        });
    }

    // ── Relationships ──────────────────────────────────────

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ── Computed Attributes ────────────────────────────────

    public function getNightsAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status !== 'paid'
            && $this->check_in->isPast();
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today())
            ->orWhereDate('check_out', today());
    }
}
