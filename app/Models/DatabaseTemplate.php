<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseTemplate extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql';
    public $timestamps = false;
    
    protected $fillable = [
        'template_name',
        'org_type',
        'template_version',
        'sql_structure',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByOrgType($query, $type)
    {
        return $query->where('org_type', $type);
    }

    public function scopeLatestVersion($query)
    {
        return $query->orderBy('template_version', 'desc');
    }

    // Méthodes utilitaires
    public static function getLatestForOrgType($orgType)
    {
        return self::active()
                  ->byOrgType($orgType)
                  ->latestVersion()
                  ->first();
    }
}
