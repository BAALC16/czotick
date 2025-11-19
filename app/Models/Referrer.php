<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referrer extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'organization_id',
        'user_id',
        'referrer_code',
        'name',
        'email',
        'phone',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commissions()
    {
        return $this->hasMany(ReferrerCommission::class);
    }

    public function registrations()
    {
        return $this->hasMany(ReferrerRegistration::class);
    }

    public function events()
    {
        // Événements avec commissions pour ce collaborateur
        return $this->hasManyThrough(
            Event::class,
            ReferrerCommission::class,
            'referrer_id', // Foreign key on referrer_commissions table
            'id', // Foreign key on events table
            'id', // Local key on referrers table
            'event_id' // Local key on referrer_commissions table
        );
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // Accessors
    public function getTotalEarningsAttribute()
    {
        return $this->registrations()
            ->where('commission_status', 'paid')
            ->sum('commission_amount');
    }

    public function getPendingEarningsAttribute()
    {
        return $this->registrations()
            ->where('commission_status', 'pending')
            ->sum('commission_amount');
    }

    // Méthodes utilitaires
    public static function generateReferrerCode()
    {
        do {
            $code = 'REF' . strtoupper(substr(uniqid(), -8));
        } while (self::where('referrer_code', $code)->exists());

        return $code;
    }
}

