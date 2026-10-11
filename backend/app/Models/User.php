<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'phone', 'gender', 'role', 'dob', 'bio', 'area', 'lat', 'long',
        'is_verified', 'credits', 'is_banned', 'hide_location', 'incognito',
        'online_at', 'phone_verified_at',
    ];

    protected $hidden = ['remember_token'];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'lat' => 'float',
            'long' => 'float',
            'is_verified' => 'boolean',
            'is_banned' => 'boolean',
            'hide_location' => 'boolean',
            'incognito' => 'boolean',
            'online_at' => 'datetime',
            'phone_verified_at' => 'datetime',
        ];
    }

    public function photos() { return $this->hasMany(UserPhoto::class)->orderBy('sort_order'); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }

    public function age(): Attribute
    {
        return Attribute::get(fn () => $this->dob?->age);
    }
}
