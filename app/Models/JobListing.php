<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'department', 'location', 'type',
        'description', 'requirements',
        'salary_min', 'salary_max', 'closing_date', 'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'closing_date' => 'date',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->is_active
            && (!$this->closing_date || $this->closing_date->isFuture());
    }
}
