<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_recipe_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'isActive',
    ];
}
