<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_store_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'area',
        'isActive',
    ];

    public function pc()
    {
        return $this->hasMany(pos_pc_desktop::class);
    }

}
