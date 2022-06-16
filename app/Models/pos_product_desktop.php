<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_product_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'product_code',
        'image',
        'name',
        'cost',
        'price',
        'isActive',
    ];

    public function product_store()
    {
        return $this->hasMany(pos_product_desktop::class);
    }
}
