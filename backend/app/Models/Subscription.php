<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'credit_package_id', 'starts_at', 'expires_at', 'features'];
    protected function casts(): array { return ['starts_at' => 'datetime', 'expires_at' => 'datetime', 'features' => 'array']; }
}
