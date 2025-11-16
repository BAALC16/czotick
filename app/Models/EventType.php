<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'type_key',
        'type_name',
        'type_description',
        'icon',
        'color',
        'default_form_fields',
        'default_settings',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'default_form_fields' => 'array',
        'default_settings' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    // Relations
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('type_name');
    }

    // Accessors
    public function getFormattedColorAttribute()
    {
        return $this->color ?: '#1a73e8';
    }

    public function getIconClassAttribute()
    {
        return 'fas fa-' . ($this->icon ?: 'calendar');
    }
}
