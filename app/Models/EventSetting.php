<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSetting extends Model
{
    use HasFactory;
    
    protected $connection = 'tenant';
    
    protected $fillable = [
        'event_id',
        'setting_key',
        'setting_value',
        'setting_type'
    ];

    protected $casts = [
        'setting_value' => 'string' // Cast selon setting_type
    ];

    // Relations
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Scopes
    public function scopeByKey($query, $key)
    {
        return $query->where('setting_key', $key);
    }

    // Accesseurs
    public function getValueAttribute()
    {
        switch ($this->setting_type) {
            case 'boolean':
                return filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($this->setting_value) ? (float) $this->setting_value : 0;
            case 'json':
                return json_decode($this->setting_value, true);
            default:
                return $this->setting_value;
        }
    }

    // Méthodes statiques utilitaires
    public static function getForEvent($eventId, $key, $default = null)
    {
        $setting = self::where('event_id', $eventId)->byKey($key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setForEvent($eventId, $key, $value, $type = 'string')
    {
        if ($type === 'json') {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? 'true' : 'false';
        }
        
        return self::updateOrCreate(
            ['event_id' => $eventId, 'setting_key' => $key],
            ['setting_value' => $value, 'setting_type' => $type]
        );
    }
}