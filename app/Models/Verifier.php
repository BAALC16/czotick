<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Verifier extends Model
{
    use HasFactory;
    
    protected $connection = 'tenant';
    
    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'access_code',
        'role',
        'allowed_zones',
        'is_active',
        'last_login'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'allowed_zones' => 'array'
    ];

    // Relations
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Accesseurs
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    // Méthodes utilitaires
    public function canAccessZone($zone)
    {
        if ($this->role === 'admin') {
            return true;
        }
        
        return in_array($zone, $this->allowed_zones ?? []);
    }

    public function updateLastLogin()
    {
        $this->update(['last_login' => now()]);
    }

    public static function generateAccessCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        } while (static::where('access_code', $code)->exists());
        
        return $code;
    }
}
