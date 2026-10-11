<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Swipe extends Model
{
    protected $fillable = ['swiper_id', 'swiped_id', 'direction'];
}
