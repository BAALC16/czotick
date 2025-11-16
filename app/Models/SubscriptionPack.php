<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPack extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pack_key',
        'pack_name',
        'pack_description',
        'pack_type',
        'commission_percentage',
        'setup_fee',
        'monthly_fee',
        'currency',
        'email_tickets',
        'whatsapp_tickets',
        'custom_tickets',
        'multi_ticket_purchase',
        'multi_country_support',
        'custom_domain',
        'advanced_analytics',
        'api_access',
        'priority_support',
        'max_events',
        'max_participants_per_event',
        'max_storage_mb',
        'max_ticket_types_per_event',
        'supported_countries',
        'payment_methods',
        'ticket_templates',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'email_tickets' => 'boolean',
        'whatsapp_tickets' => 'boolean',
        'custom_tickets' => 'boolean',
        'multi_ticket_purchase' => 'boolean',
        'multi_country_support' => 'boolean',
        'custom_domain' => 'boolean',
        'advanced_analytics' => 'boolean',
        'api_access' => 'boolean',
        'priority_support' => 'boolean',
        'max_events' => 'integer',
        'max_participants_per_event' => 'integer',
        'max_storage_mb' => 'integer',
        'max_ticket_types_per_event' => 'integer',
        'supported_countries' => 'array',
        'payment_methods' => 'array',
        'ticket_templates' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    // Relations
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function registrations()
    {
        return $this->hasMany(OrganizationRegistration::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('pack_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('pack_name');
    }

    // Accessors
    public function getFormattedCommissionAttribute()
    {
        return number_format($this->commission_percentage, 2) . '%';
    }

    public function getFormattedSetupFeeAttribute()
    {
        return number_format($this->setup_fee, 0, ',', ' ') . ' ' . $this->currency;
    }

    public function getFormattedMonthlyFeeAttribute()
    {
        return number_format($this->monthly_fee, 0, ',', ' ') . ' ' . $this->currency;
    }
}
