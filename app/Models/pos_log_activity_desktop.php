<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_log_activity_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'pic',
        'type',
        'note',
    ];
}
