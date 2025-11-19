<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferrerCommission extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'event_id',
        'referrer_id',
        'commission_rate',
        'fixed_amount',
        'commission_type',
        'notes'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
    ];

    // Relations
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function referrer()
    {
        return $this->belongsTo(Referrer::class);
    }

    // Méthodes utilitaires
    public function calculateCommission($registrationAmount)
    {
        if ($this->commission_type === 'fixed') {
            return $this->fixed_amount ?? 0;
        } else {
            return ($registrationAmount * $this->commission_rate) / 100;
        }
    }
}

