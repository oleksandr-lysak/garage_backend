<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_id',
        'photo',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }
}
