<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql'; // Base de données principale
    
    protected $fillable = [
        'org_key',
        'org_name', 
        'org_type',
        'contact_name',
        'contact_email',
        'contact_phone',
        'database_name',
        'subdomain',
        'custom_domain',
        'organization_logo',
        'subscription_pack_id',
        'enabled_event_types',
        'enabled_countries',
        'payment_methods',
        'multi_ticket_purchase',
        'max_tickets_per_purchase',
        'whatsapp_integration',
        'custom_ticket_design',
        'ticket_templates',
        'whatsapp_api_key',
        'whatsapp_phone_number'
    ];

    protected $casts = [
        'enabled_event_types' => 'array',
        'enabled_countries' => 'array',
        'payment_methods' => 'array',
        'ticket_templates' => 'array',
        'multi_ticket_purchase' => 'boolean',
        'whatsapp_integration' => 'boolean',
        'custom_ticket_design' => 'boolean',
        'max_tickets_per_purchase' => 'integer'
    ];

    // Relations
    public function users()
    {
        return $this->hasMany(SaasUser::class);
    }

    public function owner()
    {
        return $this->hasOne(SaasUser::class)->where('role', 'owner');
    }

    public function logs()
    {
        return $this->hasMany(OrganizationLog::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function metrics()
    {
        return $this->hasMany(SystemMetric::class);
    }

    // Relations supplémentaires
    public function subscriptionPack()
    {
        return $this->belongsTo(SubscriptionPack::class, 'subscription_pack_id');
    }

    public function organizationType()
    {
        return $this->belongsTo(OrganizationType::class, 'org_type', 'code');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('org_type', $type);
    }

    // Méthodes utilitaires

    public function getTenantConnection()
    {
        return "tenant_{$this->database_name}";
    }

    public function generateSubdomain()
    {
        if ($this->subdomain) {
            return $this->subdomain;
        }
        
        return \Str::slug($this->org_name);
    }
}