<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_pc_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'cashier_printer',
        'kitchen_printer',
        'bar_printer',
        'isActive',
    ];

    public function store()
    {
        return $this->belongsTo(pos_store_desktop::class);
    }

    public function deposit()
    {
        return $this->hasMany(pos_deposit_desktop::class);
    }
}
