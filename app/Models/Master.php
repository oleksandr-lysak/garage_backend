<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Master extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'password',
        'photo',
    ];

    protected $casts = [
        // 'address' => 'json',
        // 'phone' => CustomRawPhoneNumberCast::class.':INTERNATIONAL',
    ];

    protected $fillable = [
        'name',
        'password',
        'contact_phone',
        'address',
        'latitude',
        'longitude',
        'description',
        'age',
        'photo',
        'service_id',
        'tariff_id',
        'slug',
        'user_id',
        'place_id',
        'rating_google',
    ];

    // Virtual attribute to keep backward compatibility
    protected $appends = ['phone'];

    // Mutator: allow setting phone
    public function setPhoneAttribute($value): void
    {
        $this->attributes['contact_phone'] = $value;
    }

    // Accessor: provide phone
    public function getPhoneAttribute(): ?string
    {
        return $this->contact_phone ?? ($this->user->phone ?? null);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'master_services');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(MasterGallery::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }
}
