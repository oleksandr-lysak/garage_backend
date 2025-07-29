<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function masters(): HasMany
    {
        return $this->hasMany(Master::class);
    }
}
