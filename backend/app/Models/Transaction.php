<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'credit_package_id', 'razorpay_order_id', 'razorpay_payment_id', 'amount_paise', 'credits', 'type', 'status', 'meta'];
    protected function casts(): array { return ['meta' => 'array']; }
    public function user() { return $this->belongsTo(User::class); }
    public function package() { return $this->belongsTo(CreditPackage::class, 'credit_package_id'); }
}
