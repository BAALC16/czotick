<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convention extends Model
{
    use HasFactory;

    // Les attributs assignables en masse
    protected $fillable = [
        'unique_id',
        'fullname',
        'phone',
        'email',
        'organization',
        'other_organization',
        'quality',
        'ticket_type',
        'amount',
        'paymentStatus',
        'used_opening',
        'used_ag',
        'used_restau',
        'used_gala'
    ];
}
