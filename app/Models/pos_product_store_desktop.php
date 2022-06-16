<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_product_store_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'product_id',
    ];

    public function category()
    {
        return $this->belongsTo(pos_category_desktop::class);
    }

    public function product()
    {
        return $this->belongsTo(pos_product_desktop::class);
    }
}
