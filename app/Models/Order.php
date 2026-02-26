<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'booking_id', 'table_id', 'guest_id',
        'type', 'subtotal', 'tax', 'total', 'status', 'payment_status', 'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('total_price');
        $tax      = round($subtotal * 0.16, 2);
        $this->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $subtotal + $tax,
        ]);
    }
}
