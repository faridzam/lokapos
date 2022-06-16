<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_raw_material_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'quantity',
        'item_type',
        'unit',
    ];
}
