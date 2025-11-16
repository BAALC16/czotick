<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessTimeLog extends Model
{
    protected $fillable = [
        'event_id', 'access_zone', 'registration_id', 'attempt_time',
        'access_status', 'scheduled_start_time', 'scheduled_end_time',
        'verifier_id', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'attempt_time' => 'datetime',
        'scheduled_start_time' => 'datetime:H:i',
        'scheduled_end_time' => 'datetime:H:i'
    ];

    const STATUS_TOO_EARLY = 'too_early';
    const STATUS_ON_TIME = 'on_time';
    const STATUS_TOO_LATE = 'too_late';
    const STATUS_ZONE_CLOSED = 'zone_closed';

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function verifier()
    {
        return $this->belongsTo(Verifier::class);
    }

    /**
     * Obtient les logs d'accès réussis pour une inscription et une zone
     */
    public static function getSuccessfulAccessCount($registrationId, $accessZone)
    {
        return static::where('registration_id', $registrationId)
            ->where('access_zone', $accessZone)
            ->where('access_status', self::STATUS_ON_TIME)
            ->count();
    }

    /**
     * Vérifie si c'est la première tentative d'accès pour cette zone
     */
    public static function isFirstAttempt($registrationId, $accessZone)
    {
        return !static::where('registration_id', $registrationId)
            ->where('access_zone', $accessZone)
            ->exists();
    }
}
