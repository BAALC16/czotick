<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modèle pour les logs d'activité des administrateurs
 */
class AdminActivityLog extends Model
{
    use HasFactory;

    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'admin_id',
        'action',
        'resource_type',
        'resource_id',
        'description',
        'changes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    public $timestamps = false;
    
    protected $dates = ['created_at'];

    /**
     * Relation avec l'administrateur
     */
    public function admin()
    {
        return $this->belongsTo(SystemAdmin::class, 'admin_id');
    }

    /**
     * Scope pour filtrer par action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope pour filtrer par type de ressource
     */
    public function scopeByResourceType($query, $type)
    {
        return $query->where('resource_type', $type);
    }

    /**
     * Scope pour les logs récents
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Créer un log d'activité
     */
    public static function logActivity($adminId, $action, $resourceType = null, $resourceId = null, $description = null, $changes = null)
    {
        return static::create([
            'admin_id' => $adminId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}

/**
 * Modèle pour les sessions d'administration
 */
class AdminSession extends Model
{
    use HasFactory;

    protected $table = 'admin_sessions';
    
    public $incrementing = false;
    protected $keyType = 'string';

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
        'expires_at' => 'datetime'
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

/**
 * Modèle pour les widgets du dashboard
 */
class AdminDashboardWidget extends Model
{
    use HasFactory;

    protected $table = 'admin_dashboard_widgets';

    protected $fillable = [
        'admin_id',
        'widget_type',
        'widget_config',
        'position_x',
        'position_y',
        'width',
        'height',
        'is_visible'
    ];

    protected $casts = [
        'widget_config' => 'array',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_visible' => 'boolean'
    ];

    /**
     * Relation avec l'administrateur
     */
    public function admin()
    {
        return $this->belongsTo(SystemAdmin::class, 'admin_id');
    }

    /**
     * Scope pour les widgets visibles
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope pour ordonner par position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position_y')->orderBy('position_x');
    }

    /**
     * Types de widgets disponibles
     */
    public static function getAvailableTypes()
    {
        return [
            'stats_organizations' => 'Statistiques des organisations',
            'stats_users' => 'Statistiques des utilisateurs',
            'recent_activity' => 'Activité récente',
            'system_alerts' => 'Alertes système',
            'revenue_chart' => 'Graphique des revenus',
            'growth_chart' => 'Graphique de croissance',
            'top_organizations' => 'Organisations principales',
            'system_health' => 'Santé du système'
        ];
    }
}

/**
 * Modèle pour les alertes système
 */
class SystemAlert extends Model
{
    use HasFactory;

    protected $table = 'system_alerts';

    protected $fillable = [
        'alert_type',
        'severity',
        'title',
        'message',
        'data',
        'is_resolved',
        'resolved_by',
        'resolved_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime'
    ];

    /**
     * Types d'alertes
     */
    public const ALERT_TYPES = [
        'security' => 'Sécurité',
        'performance' => 'Performance',
        'billing' => 'Facturation',
        'system' => 'Système',
        'warning' => 'Avertissement'
    ];

    /**
     * Niveaux de sévérité
     */
    public const SEVERITY_LEVELS = [
        'low' => 'Faible',
        'medium' => 'Moyen',
        'high' => 'Élevé',
        'critical' => 'Critique'
    ];

    /**
     * Relation avec l'administrateur qui a résolu l'alerte
     */
    public function resolvedBy()
    {
        return $this->belongsTo(SystemAdmin::class, 'resolved_by');
    }

    /**
     * Scope pour les alertes non résolues
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope pour les alertes par sévérité
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope pour les alertes critiques
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope pour les alertes récentes
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Marquer l'alerte comme résolue
     */
    public function resolve($adminId = null, $notes = null)
    {
        $this->update([
            'is_resolved' => true,
            'resolved_by' => $adminId ?? auth('system_admin')->id(),
            'resolved_at' => now()
        ]);

        // Logger la résolution
        AdminActivityLog::logActivity(
            $adminId ?? auth('system_admin')->id(),
            'resolve_alert',
            'system_alert',
            $this->id,
            "Alerte résolue: {$this->title}" . ($notes ? " - Notes: {$notes}" : '')
        );
    }

    /**
     * Créer une nouvelle alerte
     */
    public static function createAlert($type, $severity, $title, $message, $data = null)
    {
        return static::create([
            'alert_type' => $type,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Obtenir le libellé du type d'alerte
     */
    public function getAlertTypeLabelAttribute()
    {
        return self::ALERT_TYPES[$this->alert_type] ?? $this->alert_type;
    }

    /**
     * Obtenir le libellé de la sévérité
     */
    public function getSeverityLabelAttribute()
    {
        return self::SEVERITY_LEVELS[$this->severity] ?? $this->severity;
    }

    /**
     * Obtenir la couleur CSS selon la sévérité
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'low' => 'text-blue-600',
            'medium' => 'text-yellow-600',
            'high' => 'text-orange-600',
            'critical' => 'text-red-600',
            default => 'text-gray-600'
        };
    }
}

/**
 * Modèle pour les tâches planifiées
 */
class ScheduledTask extends Model
{
    use HasFactory;

    protected $table = 'scheduled_tasks';

    protected $fillable = [
        'task_name',
        'task_type',
        'schedule_expression',
        'command',
        'last_run_at',
        'next_run_at',
        'last_duration_seconds',
        'last_status',
        'last_output',
        'is_active'
    ];

    protected $casts = [
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'last_duration_seconds' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Statuts des tâches
     */
    public const TASK_STATUSES = [
        'success' => 'Succès',
        'failed' => 'Échec',
        'running' => 'En cours'
    ];

    /**
     * Scope pour les tâches actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les tâches à exécuter
     */
    public function scopeDue($query)
    {
        return $query->where('next_run_at', '<=', now())
                    ->where('is_active', true);
    }

    /**
     * Marquer la tâche comme en cours
     */
    public function markAsRunning()
    {
        $this->update([
            'last_status' => 'running',
            'last_run_at' => now()
        ]);
    }

    /**
     * Marquer la tâche comme terminée
     */
    public function markAsCompleted($output = null, $duration = null)
    {
        $this->update([
            'last_status' => 'success',
            'last_output' => $output,
            'last_duration_seconds' => $duration,
            'next_run_at' => $this->calculateNextRun()
        ]);
    }

    /**
     * Marquer la tâche comme échouée
     */
    public function markAsFailed($output = null, $duration = null)
    {
        $this->update([
            'last_status' => 'failed',
            'last_output' => $output,
            'last_duration_seconds' => $duration,
            'next_run_at' => $this->calculateNextRun()
        ]);
    }

    /**
     * Calculer la prochaine exécution (basique)
     */
    private function calculateNextRun()
    {
        // Implémentation basique - à améliorer avec une bibliothèque cron
        return now()->addHour();
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getLastStatusLabelAttribute()
    {
        return self::TASK_STATUSES[$this->last_status] ?? $this->last_status;
    }
}