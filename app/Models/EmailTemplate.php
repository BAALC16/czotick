<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;
    
    protected $connection = 'tenant';
    
    protected $fillable = [
        'event_id',
        'template_name',
        'template_type',
        'subject',
        'content',
        'available_variables',
        'is_active'
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean'
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

    public function scopeByType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    // Méthodes utilitaires
    public function renderForRegistration(Registration $registration)
    {
        $variables = [
            'participant_name' => $registration->fullname,
            'event_title' => $registration->event->event_title,
            'ticket_type' => $registration->ticketType->ticket_name,
            'ticket_price' => $registration->ticket_price,
            'registration_number' => $registration->registration_number,
            'event_date' => $registration->event->event_date->format('d/m/Y'),
            'event_location' => $registration->event->event_location,
        ];
        
        $subject = $this->replaceVariables($this->subject, $variables);
        $content = $this->replaceVariables($this->content, $variables);
        
        return [
            'subject' => $subject,
            'content' => $content
        ];
    }

    private function replaceVariables($text, $variables)
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        
        return $text;
    }
}