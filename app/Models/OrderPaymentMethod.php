<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPaymentMethod extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'amount',
    ];
}
