<?php

// app/Models/EventSchedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    protected $fillable = [
        'event_type',
        'event_name',
        'start_time',
        'end_time',
        'active'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'active' => 'boolean'
    ];
}