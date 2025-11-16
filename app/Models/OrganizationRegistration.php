<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationRegistration extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'registration_token',
        'org_name',
        'org_key',
        'org_type',
        'contact_name',
        'contact_email',
        'contact_phone',
        'subdomain',
        'custom_domain',
        'subscription_pack_id',
        'status',
        'pack_settings',
        'enabled_countries',
        'enabled_event_types',
        'error_message',
        'processed_at',
        'created_organization_id',
        'created_database_name'
    ];

    protected $casts = [
        'pack_settings' => 'array',
        'enabled_countries' => 'array',
        'enabled_event_types' => 'array',
        'processed_at' => 'datetime'
    ];

    // Relations
    public function subscriptionPack()
    {
        return $this->belongsTo(SubscriptionPack::class);
    }

    public function createdOrganization()
    {
        return $this->belongsTo(Organization::class, 'created_organization_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
