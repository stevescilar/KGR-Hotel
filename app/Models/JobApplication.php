<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'job_listing_id', 'first_name', 'last_name',
        'email', 'phone', 'cv_path', 'cover_letter_path',
        'message', 'status', 'hr_notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (JobApplication $app) {
            $app->reference = 'APP-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
        });
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }
}
