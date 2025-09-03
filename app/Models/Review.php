<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'review',
        'rating',
        'master_id',
        'user_id',
    ];

    protected $casts = [
        'rating' => 'integer',
        'master_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Get the master that owns the review
     */
    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }

    /**
     * Get the user that wrote the review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for verified reviews only
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_phone', true);
    }

    /**
     * Scope for recent reviews
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
