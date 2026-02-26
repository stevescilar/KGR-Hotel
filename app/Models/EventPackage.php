<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'starting_price',
        'min_guests', 'max_guests', 'inclusions', 'images', 'is_active',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'images'     => 'array',
        'is_active'  => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(EventBooking::class);
    }
}
