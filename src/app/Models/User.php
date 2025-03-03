<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


/**
 * @method bool hasVerifiedEmail()
 * @method bool update(array $attributes = [], array $options = [])
 */

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'has_logged_in',
        'first_login',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        'email_verified_at' => 'datetime',
        'first_login' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
