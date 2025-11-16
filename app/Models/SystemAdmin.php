<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class SystemAdmin extends Authenticatable
{
    use HasFactory;

    protected $table = 'system_admins';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'admin_level',
        'permissions',
        'is_active',
        'must_change_password',
        'two_factor_enabled',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'email_verified_at' => 'datetime',
        'login_attempts' => 'integer',
    ];

    /**
     * Constantes pour les niveaux d'administration
     */
    public const ADMIN_LEVELS = [
        'super_admin' => 'Super Administrateur',
        'admin' => 'Administrateur', 
        'support' => 'Support',
        'readonly' => 'Lecture seule'
    ];

    /**
     * Mutateur pour hasher le mot de passe
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Accesseur pour le nom complet
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Accesseur pour le libellé du niveau d'admin
     */
    public function getAdminLevelLabelAttribute()
    {
        return self::ADMIN_LEVELS[$this->admin_level] ?? $this->admin_level;
    }

    /**
     * Accesseur pour vérifier si le compte est verrouillé
     */
    public function getIsLockedAttribute()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Scope pour les administrateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les administrateurs non verrouillés
     */
    public function scopeNotLocked($query)
    {
        return $query->where(function($q) {
            $q->whereNull('locked_until')
              ->orWhere('locked_until', '<=', now());
        });
    }

    /**
     * Scope par niveau d'administration
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('admin_level', $level);
    }

    /**
     * Vérifier si l'admin a une permission spécifique
     */
    public function hasPermission($permission)
    {
        // Super admin a toutes les permissions
        if ($this->admin_level === 'super_admin') {
            return true;
        }

        $permissions = $this->permissions ?? [];
        
        // Vérifier la permission exacte
        if (in_array($permission, $permissions)) {
            return true;
        }

        // Vérifier les permissions avec wildcard
        foreach ($permissions as $userPermission) {
            if (str_ends_with($userPermission, '*')) {
                $prefix = str_replace('*', '', $userPermission);
                if (str_starts_with($permission, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Vérifier si l'admin peut effectuer une action sur une ressource
     */
    public function can($action, $resource = null)
    {
        if ($resource) {
            return $this->hasPermission("{$resource}.{$action}");
        }
        
        return $this->hasPermission($action);
    }

    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin($ipAddress = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress ?? request()->ip(),
            'login_attempts' => 0 // Reset attempts on successful login
        ]);
    }

    /**
     * Créer un nouvel administrateur
     */
    public static function createAdmin(array $data, $createdBy = null)
    {
        return static::create(array_merge($data, [
            'created_by' => $createdBy,
            'must_change_password' => false,
            'is_active' => true
        ]));
    }
}