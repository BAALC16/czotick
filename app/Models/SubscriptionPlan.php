<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql';
    public $timestamps = false;
    
    protected $fillable = [
        'plan_name',
        'plan_code',
        'monthly_price',
        'yearly_price',
        'setup_fee',
        'max_events',
        'max_participants_per_event',
        'max_storage_mb',
        'max_users',
        'features',
        'is_active'
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'max_events' => 'integer',
        'max_participants_per_event' => 'integer',
        'max_storage_mb' => 'integer',
        'max_users' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean'
    ];

    // Relations
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'subscription_plan', 'plan_code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('plan_code', $code);
    }

    // Accesseurs
    public function getIsFreeAttribute()
    {
        return $this->monthly_price == 0 && $this->yearly_price == 0;
    }

    public function getYearlySavingsAttribute()
    {
        $yearlyEquivalent = $this->monthly_price * 12;
        return $yearlyEquivalent - $this->yearly_price;
    }

    public function getYearlySavingsPercentageAttribute()
    {
        $yearlyEquivalent = $this->monthly_price * 12;
        if ($yearlyEquivalent == 0) {
            return 0;
        }
        
        return round(($this->yearly_savings / $yearlyEquivalent) * 100, 1);
    }

    // Méthodes utilitaires
    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    public function isUnlimited($resource)
    {
        $property = "max_{$resource}";
        return $this->$property === -1;
    }
}
