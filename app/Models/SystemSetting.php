<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql';
    public $timestamps = false;
    
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

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

    // Méthodes utilitaires
    public static function get($key, $default = null)
    {
        $setting = self::byKey($key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $type = 'string')
    {
        $setting = self::byKey($key)->first();
        
        if ($type === 'json') {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? 'true' : 'false';
        }
        
        if ($setting) {
            $setting->update(['setting_value' => $value]);
        } else {
            self::create([
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => $type
            ]);
        }
    }
}