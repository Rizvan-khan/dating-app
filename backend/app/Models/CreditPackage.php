<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPackage extends Model
{
    protected $fillable = ['name', 'price_paise', 'credits', 'bonus_credits', 'valid_days', 'badge', 'features', 'is_active'];
    protected function casts(): array { return ['features' => 'array', 'is_active' => 'boolean']; }
}
