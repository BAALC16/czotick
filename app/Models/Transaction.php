<?php

// app/Models/Transaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'fullname',
        'organization',
        'other_organization',
        'quality',
        'ticket_type',
        'reference_czotic',
        'reference_wave',
        'status',
    ];
}
