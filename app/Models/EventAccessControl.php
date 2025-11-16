<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventAccessControl extends Model
{
    use HasFactory;
    
    protected $connection = 'tenant';
    public $timestamps = false;
    
    protected $fillable = [
        'event_id',
        'access_zone',
        'zone_name',
        'zone_description',
        'requires_separate_check',
        'max_capacity',
        'access_start_time',
        'access_end_time',
        'access_date',
        'timezone',
        'early_access_minutes',
        'late_access_minutes',
        'allowed_ticket_types',
        'is_active'
    ];

    protected $casts = [
        'requires_separate_check' => 'boolean',
        'is_active' => 'boolean',
        'access_date' => 'date',
        'access_start_time' => 'datetime:H:i',
        'access_end_time' => 'datetime:H:i',
        'allowed_ticket_types' => 'array',
        'created_at' => 'datetime',
        'early_access_minutes' => 'integer',
        'late_access_minutes' => 'integer'
    ];

    // Relations
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function accessTimeLogs(): HasMany
    {
        return $this->hasMany(AccessTimeLog::class, 'access_zone', 'access_zone')
            ->where('event_id', $this->event_id);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByZone($query, $zone)
    {
        return $query->where('access_zone', $zone);
    }

    public function scopeForTicketType($query, $ticketTypeId)
    {
        return $query->where(function($q) use ($ticketTypeId) {
            $q->whereNull('allowed_ticket_types')
              ->orWhereJsonContains('allowed_ticket_types', $ticketTypeId);
        });
    }

    // Accesseurs
    public function getIsCurrentlyAccessibleAttribute()
    {
        if (!$this->is_active) {
            return false;
        }
        
        $accessStatus = $this->determineAccessStatus();
        return $accessStatus['status'] === AccessTimeLog::STATUS_ON_TIME;
    }

    public function getZoneSlugAttribute()
    {
        return Str::slug($this->access_zone);
    }

    public function getFormattedScheduleAttribute()
    {
        if (!$this->access_start_time || !$this->access_end_time) {
            return 'Accès libre';
        }

        $start = Carbon::parse($this->access_start_time)->format('H:i');
        $end = Carbon::parse($this->access_end_time)->format('H:i');
        
        return "{$start} - {$end}";
    }

    public function getAccessWindowAttribute()
    {
        if (!$this->access_start_time || !$this->access_end_time) {
            return null;
        }

        $timezone = $this->timezone ?? 'Africa/Abidjan';
        $startTime = Carbon::createFromTimeString($this->access_start_time, $timezone);
        $endTime = Carbon::createFromTimeString($this->access_end_time, $timezone);
        
        // Ajouter les minutes d'accès anticipé et tardif
        $earlyStart = (clone $startTime)->subMinutes($this->early_access_minutes ?? 0);
        $lateEnd = (clone $endTime)->addMinutes($this->late_access_minutes ?? 0);

        return [
            'official_start' => $startTime->format('H:i'),
            'official_end' => $endTime->format('H:i'),
            'early_start' => $earlyStart->format('H:i'),
            'late_end' => $lateEnd->format('H:i'),
            'early_minutes' => $this->early_access_minutes ?? 0,
            'late_minutes' => $this->late_access_minutes ?? 0
        ];
    }

    // Méthodes utilitaires existantes
    public function isTicketTypeAllowed($ticketTypeId)
    {
        if (!$this->allowed_ticket_types) {
            return true; // Tous les types autorisés si pas de restriction
        }
        
        return in_array($ticketTypeId, $this->allowed_ticket_types);
    }

    /**
     * Alias pour compatibilité avec le code existant
     */
    public function hasTicketTypeAccess($ticketTypeId)
    {
        return $this->isTicketTypeAllowed($ticketTypeId);
    }

    /**
     * Détermine le statut d'accès basé sur l'heure actuelle
     */
    public function determineAccessStatus($currentTime = null)
    {
        $timezone = $this->timezone ?? 'Africa/Abidjan';
        $currentTime = $currentTime ?? now($timezone);

        // Vérifier si la zone est active
        if (!$this->is_active) {
            return [
                'status' => AccessTimeLog::STATUS_ZONE_CLOSED,
                'message' => "La zone {$this->zone_name} est fermée",
                'scheduled_start' => null,
                'scheduled_end' => null
            ];
        }

        // Vérifier la date d'accès si définie
        if ($this->access_date) {
            $accessDate = Carbon::parse($this->access_date, $timezone);
            if (!$currentTime->isSameDay($accessDate)) {
                return [
                    'status' => AccessTimeLog::STATUS_ZONE_CLOSED,
                    'message' => "Accès autorisé uniquement le {$accessDate->format('d/m/Y')}",
                    'scheduled_start' => null,
                    'scheduled_end' => null
                ];
            }
        }

        // Vérifier les horaires si définis
        if ($this->access_start_time && $this->access_end_time) {
            $startTime = Carbon::createFromTimeString($this->access_start_time, $timezone);
            $endTime = Carbon::createFromTimeString($this->access_end_time, $timezone);
            
            // Calculer les fenêtres d'accès avec tolérance
            $earlyStartTime = (clone $startTime)->subMinutes($this->early_access_minutes ?? 0);
            $lateEndTime = (clone $endTime)->addMinutes($this->late_access_minutes ?? 0);

            $scheduledStart = $startTime->format('H:i:s');
            $scheduledEnd = $endTime->format('H:i:s');

            if ($currentTime->lt($earlyStartTime)) {
                return [
                    'status' => AccessTimeLog::STATUS_TOO_EARLY,
                    'message' => "Accès autorisé à partir de {$startTime->format('H:i')}",
                    'scheduled_start' => $scheduledStart,
                    'scheduled_end' => $scheduledEnd
                ];
            }

            if ($currentTime->gt($lateEndTime)) {
                return [
                    'status' => AccessTimeLog::STATUS_TOO_LATE,
                    'message' => "Accès fermé depuis {$endTime->format('H:i')}",
                    'scheduled_start' => $scheduledStart,
                    'scheduled_end' => $scheduledEnd
                ];
            }

            return [
                'status' => AccessTimeLog::STATUS_ON_TIME,
                'message' => "Accès autorisé",
                'scheduled_start' => $scheduledStart,
                'scheduled_end' => $scheduledEnd
            ];
        }

        // Pas d'horaires définis = accès libre
        return [
            'status' => AccessTimeLog::STATUS_ON_TIME,
            'message' => "Accès libre",
            'scheduled_start' => null,
            'scheduled_end' => null
        ];
    }

    /**
     * Obtient le nombre d'accès réussis pour une inscription
     */
    public function getSuccessfulAccessCountForRegistration($registrationId)
    {
        return AccessTimeLog::on('tenant')
            ->where('registration_id', $registrationId)
            ->where('access_zone', $this->access_zone)
            ->where('access_status', AccessTimeLog::STATUS_ON_TIME)
            ->count();
    }

    /**
     * Obtient le nombre total d'accès pour une inscription (tous statuts)
     */
    public function getTotalAccessCountForRegistration($registrationId)
    {
        return AccessTimeLog::on('tenant')
            ->where('registration_id', $registrationId)
            ->where('access_zone', $this->access_zone)
            ->count();
    }

    /**
     * Vérifie si c'est la première tentative d'accès pour cette inscription
     */
    public function isFirstAttemptForRegistration($registrationId)
    {
        return !AccessTimeLog::on('tenant')
            ->where('registration_id', $registrationId)
            ->where('access_zone', $this->access_zone)
            ->exists();
    }

    /**
     * Obtient les statistiques d'occupation actuelle
     */
    public function getCurrentOccupancyStats()
    {
        $today = now()->startOfDay();
        
        $stats = AccessTimeLog::on('tenant')
            ->where('access_zone', $this->access_zone)
            ->where('event_id', $this->event_id)
            ->where('attempt_time', '>=', $today)
            ->selectRaw('
                access_status,
                COUNT(*) as total_attempts,
                COUNT(DISTINCT registration_id) as unique_participants
            ')
            ->groupBy('access_status')
            ->get()
            ->keyBy('access_status');

        $successful = $stats->get(AccessTimeLog::STATUS_ON_TIME);
        $currentOccupancy = $successful ? $successful->unique_participants : 0;

        return [
            'current_occupancy' => $currentOccupancy,
            'max_capacity' => $this->max_capacity,
            'occupancy_rate' => $this->max_capacity ? ($currentOccupancy / $this->max_capacity) * 100 : null,
            'is_full' => $this->max_capacity ? $currentOccupancy >= $this->max_capacity : false,
            'stats_by_status' => $stats,
            'calculated_at' => now()->toISOString()
        ];
    }

    /**
     * Obtient l'historique des accès par heure
     */
    public function getAccessTimeline($date = null)
    {
        $targetDate = $date ? Carbon::parse($date) : now();
        
        return AccessTimeLog::on('tenant')
            ->where('access_zone', $this->access_zone)
            ->where('event_id', $this->event_id)
            ->whereDate('attempt_time', $targetDate->toDateString())
            ->selectRaw('
                HOUR(attempt_time) as hour,
                access_status,
                COUNT(*) as count
            ')
            ->groupBy('hour', 'access_status')
            ->orderBy('hour')
            ->get()
            ->groupBy('hour');
    }

    /**
     * Vérifie si la capacité maximale est atteinte
     */
    public function isCapacityReached()
    {
        if (!$this->max_capacity) {
            return false;
        }

        $occupancy = $this->getCurrentOccupancyStats();
        return $occupancy['is_full'];
    }

    /**
     * Obtient les informations de debug pour les développeurs
     */
    public function getDebugInfo($currentTime = null)
    {
        $timezone = $this->timezone ?? 'Africa/Abidjan';
        $currentTime = $currentTime ?? now($timezone);
        $accessStatus = $this->determineAccessStatus($currentTime);
        $occupancy = $this->getCurrentOccupancyStats();

        return [
            'zone_info' => [
                'id' => $this->id,
                'access_zone' => $this->access_zone,
                'zone_name' => $this->zone_name,
                'is_active' => $this->is_active,
                'requires_separate_check' => $this->requires_separate_check
            ],
            'schedule' => $this->access_window,
            'current_time' => $currentTime->format('Y-m-d H:i:s T'),
            'access_status' => $accessStatus,
            'capacity' => $occupancy,
            'ticket_restrictions' => [
                'allowed_ticket_types' => $this->allowed_ticket_types,
                'has_restrictions' => !empty($this->allowed_ticket_types)
            ]
        ];
    }

    /**
     * Méthodes pour l'API JSON
     */
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'access_zone' => $this->access_zone,
            'zone_name' => $this->zone_name,
            'zone_slug' => $this->zone_slug,
            'zone_description' => $this->zone_description,
            'schedule' => $this->formatted_schedule,
            'access_window' => $this->access_window,
            'max_capacity' => $this->max_capacity,
            'requires_separate_check' => $this->requires_separate_check,
            'allowed_ticket_types' => $this->allowed_ticket_types,
            'is_active' => $this->is_active,
            'is_currently_accessible' => $this->is_currently_accessible,
            'current_status' => $this->determineAccessStatus()
        ];
    }
}