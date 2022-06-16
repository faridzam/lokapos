<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_saved_cart_desktop extends Model
{
    use HasFactory;


    protected $fillable = [
        'no_invoice',
        'pc_id',
        'store_id',
        'cashier_id',
        'bill_amount',
        'note',
    ];
}
