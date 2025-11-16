<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql';
    public $timestamps = false;
    
    protected $fillable = [
        'organization_id',
        'user_id',
        'title',
        'message',
        'type',
        'send_email',
        'send_sms',
        'show_in_app',
        'is_read',
        'sent_at',
        'read_at'
    ];

    protected $casts = [
        'send_email' => 'boolean',
        'send_sms' => 'boolean',
        'show_in_app' => 'boolean',
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(SaasUser::class, 'user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForApp($query)
    {
        return $query->where('show_in_app', true);
    }

    // Méthodes utilitaires
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public static function createForOrganization($organizationId, $title, $message, $type = 'info', $options = [])
    {
        return self::create(array_merge([
            'organization_id' => $organizationId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'send_email' => false,
            'send_sms' => false,
            'show_in_app' => true
        ], $options));
    }
}