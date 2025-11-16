<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportedCountry extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'country_code',
        'country_name',
        'country_name_fr',
        'phone_code',
        'currency_code',
        'currency_symbol',
        'flag_emoji',
        'payment_providers',
        'phone_format',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'payment_providers' => 'array',
        'phone_format' => 'array',
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
        return $query->orderBy('display_order')->orderBy('country_name');
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->country_name_fr ?: $this->country_name;
    }

    public function getFormattedPhoneCodeAttribute()
    {
        return $this->phone_code ?: '+225';
    }

    public function getFormattedCurrencyAttribute()
    {
        return $this->currency_symbol . ' (' . $this->currency_code . ')';
    }
}
