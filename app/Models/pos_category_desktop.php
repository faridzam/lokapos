<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_category_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'isActive',
    ];

    public function product_store()
    {
        return $this->hasMany(pos_product_store_desktop::class);
    }
}
