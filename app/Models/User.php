<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;

/**
 * @property string|null $email
 * @property string|null $email_verified_at
 * @property string|null $user_code
 * @property string|null $telegram
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable;
    use MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];

    public function getJWTCustomClaims(): array
    {
        return [
            'phone' => $this->phone,
        ];
    }

    public function master(): HasOne
    {
        return $this->hasOne(Master::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }
}
