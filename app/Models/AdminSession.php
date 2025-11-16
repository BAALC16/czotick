<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modèle pour les sessions d'administration
 */
class AdminSession extends Model
{
    use HasFactory;

    protected $table = 'admin_sessions';
    
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Table n'a que created_at

    protected $fillable = [
        'id',
        'admin_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
        'expires_at'
    ];

    protected $casts = [
        'last_activity' => 'integer',
        'expires_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    /**
     * Relation avec l'administrateur
     */
    public function admin()
    {
        return $this->belongsTo(SystemAdmin::class, 'admin_id');
    }

    /**
     * Scope pour les sessions actives
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope pour les sessions expirées
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Vérifier si la session est expirée
     */
    public function isExpired()
    {
        return $this->expires_at <= now();
    }

    /**
     * Nettoyer les sessions expirées
     */
    public static function cleanupExpired()
    {
        return static::expired()->delete();
    }
}