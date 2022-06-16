<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_order_detail_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'order_id',
        'product_id',
        'qty',
        'subtotal',
        'discount',
        'specialPrice',
        'note',
        'isActive',
    ];
}
