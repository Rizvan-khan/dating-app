<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['match_id', 'sender_id', 'body', 'read_at'];
    protected function casts(): array { return ['read_at' => 'datetime']; }
}
