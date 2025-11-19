<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferrerRegistration extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'registration_id',
        'referrer_id',
        'event_id',
        'registration_amount',
        'commission_amount',
        'commission_status',
        'paid_at'
    ];

    protected $casts = [
        'registration_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relations
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function referrer()
    {
        return $this->belongsTo(Referrer::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('commission_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('commission_status', 'paid');
    }

    // Méthodes utilitaires
    public function markAsPaid()
    {
        $this->update([
            'commission_status' => 'paid',
            'paid_at' => now()
        ]);
    }
}

