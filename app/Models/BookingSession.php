<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSession extends Model
{
    protected $fillable = [
        'name',
        'day_type',
        'start_time',
        'end_time',
        'is_active',
    ];
}