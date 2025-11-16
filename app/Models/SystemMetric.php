<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemMetric extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql';
    public $timestamps = false;
    
    protected $fillable = [
        'organization_id',
        'metric_name',
        'metric_value',
        'metric_unit',
        'measured_at',
        'period_type'
    ];

    protected $casts = [
        'metric_value' => 'decimal:4',
        'measured_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Scopes
    public function scopeByMetric($query, $metricName)
    {
        return $query->where('metric_name', $metricName);
    }

    public function scopeByPeriod($query, $periodType)
    {
        return $query->where('period_type', $periodType);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('measured_at', '>=', now()->subDays($days));
    }

    // Méthodes utilitaires
    public static function recordMetric($organizationId, $metricName, $value, $unit = null, $periodType = 'daily')
    {
        return self::create([
            'organization_id' => $organizationId,
            'metric_name' => $metricName,
            'metric_value' => $value,
            'metric_unit' => $unit,
            'measured_at' => now(),
            'period_type' => $periodType
        ]);
    }
}