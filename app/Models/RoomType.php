<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'max_adults', 'max_children',
        'base_price', 'weekend_price', 'amenities', 'images', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images'    => 'array',
        'is_active' => 'boolean',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }

    public function getPriceForDate(Carbon $date): float
    {
        $rule = $this->pricingRules()
            ->where('is_active', true)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($rule) return $rule->price;
        if ($date->isWeekend() && $this->weekend_price) return $this->weekend_price;

        return $this->base_price;
    }
}
