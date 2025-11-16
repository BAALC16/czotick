<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationType extends Model
{
    use HasFactory;
    
    protected $connection = 'saas_master';
    
    protected $fillable = [
        'code',
        'name',
        'name_fr',
        'description',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // Relations
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'org_type', 'code');
    }

    // Accesseurs
    public function getDisplayNameAttribute()
    {
        return $this->name_fr ?: $this->name;
    }
}

