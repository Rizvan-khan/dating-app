<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeartMatch extends Model
{
    protected $table = 'matches';
    protected $fillable = ['user_one_id', 'user_two_id', 'matched_at'];
    protected function casts(): array { return ['matched_at' => 'datetime']; }
    public function messages() { return $this->hasMany(Message::class, 'match_id'); }
}
