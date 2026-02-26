<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'phone',
        'id_number', 'nationality', 'address', 'vip_tier', 'loyalty_points',
    ];

    protected $hidden = ['id_number'];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function addPoints(int $points, string $type, string $description, Model $reference): void
    {
        $this->increment('loyalty_points', $points);

        $this->loyaltyTransactions()->create([
            'points'             => $points,
            'type'               => $type,
            'description'        => $description,
            'referenceable_type' => get_class($reference),
            'referenceable_id'   => $reference->id,
            'balance_after'      => $this->fresh()->loyalty_points,
        ]);

        $this->updateTier();
    }

    private function updateTier(): void
    {
        $points = $this->loyalty_points;
        $tier = match (true) {
            $points >= 10000 => 'gold',
            $points >= 5000  => 'silver',
            $points >= 1000  => 'bronze',
            default          => 'none',
        };
        $this->update(['vip_tier' => $tier]);
    }
}
