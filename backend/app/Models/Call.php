<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = ['match_id', 'caller_id', 'receiver_id', 'type', 'started_at', 'ended_at', 'duration_seconds', 'credits_charged', 'status'];
    protected function casts(): array { return ['started_at' => 'datetime', 'ended_at' => 'datetime']; }
}
