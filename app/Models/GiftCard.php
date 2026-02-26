<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'original_value', 'remaining_value',
        'purchased_by_name', 'purchased_by_email',
        'recipient_name', 'recipient_email', 'message',
        'expires_at', 'status',
    ];

    protected $casts = ['expires_at' => 'date'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (GiftCard $gc) {
            $gc->code = 'KGR-GC-' . strtoupper(substr(md5(uniqid()), 0, 10));
        });
    }

    public function isValid(): bool
    {
        return $this->status === 'active'
            && $this->remaining_value > 0
            && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
