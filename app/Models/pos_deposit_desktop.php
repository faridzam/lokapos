<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_deposit_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'pc_id',
        'cashier_id',
        'pec100',
        'pec50',
        'pec20',
        'pec10',
        'pec5',
        'pec2',
        'pec1',
        'total',
    ];

    public function cashier()
    {
        return $this->belongsTo(pos_pc_desktop::class);
    }
}
