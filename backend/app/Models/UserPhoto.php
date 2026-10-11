<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model
{
    protected $fillable = ['user_id', 'path', 'is_primary', 'status', 'sort_order'];
    protected function casts(): array { return ['is_primary' => 'boolean']; }
    public function user() { return $this->belongsTo(User::class); }
}
