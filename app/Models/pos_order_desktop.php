<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_order_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'pc_id',
        'store_id',
        'cashier_id',
        'payment_id',
        'bill_amount',
        'pay_amount',
        'change_amount',
        'note',
        'tax',
        'discount',
        'isActive'
    ];
}
