<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Carbon\Carbon;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id', 'room_number', 'floor', 'cottage', 'status', 'notes',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(RoomBlock::class);
    }

    public function isAvailable(Carbon $from, Carbon $to): bool
    {
        if ($this->status === 'maintenance') return false;

        $hasBlock = $this->blocks()
            ->where('blocked_from', '<=', $to)
            ->where('blocked_to', '>=', $from)
            ->exists();

        if ($hasBlock) return false;

        return !$this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in', '<', $to)
            ->where('check_out', '>', $from)
            ->exists();
    }
}
