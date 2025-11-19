<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $connection = 'tenant';
    
    protected $fillable = [
        'event_title',
        'event_description',
        'event_slug',
        'event_date',
        'event_start_time',
        'event_end_time',
        'event_location',
        'event_address',
        'event_image',
        'organization_logo',
        'event_banner',
        'primary_color',
        'secondary_color',
        'dress_code_men',
        'dress_code_women',
        'dress_code_general',
        'registration_start_date',
        'registration_end_date',
        'max_participants',
        'is_published',
        'requires_approval',
        'referrer_code'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_start_time' => 'datetime:H:i',
        'event_end_time' => 'datetime:H:i',
        'registration_start_date' => 'datetime',
        'registration_end_date' => 'datetime',
        'max_participants' => 'integer',
        'is_published' => 'boolean',
        'requires_approval' => 'boolean'
    ];

    // Relations
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class)->orderBy('display_order');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function confirmedRegistrations()
    {
        return $this->hasMany(Registration::class)->where('status', 'confirmed');
    }

    public function paidRegistrations()
    {
        return $this->hasMany(Registration::class)->where('payment_status', 'paid');
    }

    public function accessControls()
    {
        return $this->hasMany(EventAccessControl::class);
    }

    public function verifiers()
    {
        return $this->hasMany(Verifier::class);
    }

    public function settings()
    {
        return $this->hasMany(EventSetting::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function referrer()
    {
        return $this->belongsTo(Referrer::class, 'referrer_code', 'referrer_code');
    }

    public function referrerCommissions()
    {
        return $this->hasMany(ReferrerCommission::class);
    }

    public function referrerRegistrations()
    {
        return $this->hasMany(ReferrerRegistration::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now()->toDateString());
    }

    public function scopeOpenForRegistration($query)
    {
        return $query->where('registration_start_date', '<=', now())
                    ->where('registration_end_date', '>=', now());
    }

    // Accesseurs
    public function getRegistrationStatusAttribute()
    {
        $now = now();
        
        if ($this->registration_start_date && $now < $this->registration_start_date) {
            return 'not_started';
        }
        
        if ($this->registration_end_date && $now > $this->registration_end_date) {
            return 'closed';
        }
        
        if ($this->max_participants && $this->confirmed_registrations_count >= $this->max_participants) {
            return 'full';
        }
        
        return 'open';
    }

    public function getAvailableSpotsAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        
        $confirmedCount = $this->confirmedRegistrations()->count();
        return max(0, $this->max_participants - $confirmedCount);
    }

    public function getTotalRevenueAttribute()
    {
        return $this->paidRegistrations()->sum('amount_paid');
    }

    public function getExpectedRevenueAttribute()
    {
        return $this->registrations()->sum('ticket_price');
    }

    public function getRegistrationProgressAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        
        $confirmedCount = $this->confirmedRegistrations()->count();
        return round(($confirmedCount / $this->max_participants) * 100, 1);
    }

    public function getCanRegisterAttribute()
    {
        return $this->is_published && $this->registration_status === 'open';
    }

    // Méthodes utilitaires
    public function generateSlug()
    {
        $baseSlug = \Str::slug($this->event_title);
        $slug = $baseSlug;
        $counter = 1;
        
        while (self::where('event_slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    public function getRegistrationStats()
    {
        return [
            'total' => $this->registrations()->count(),
            'confirmed' => $this->confirmedRegistrations()->count(),
            'paid' => $this->paidRegistrations()->count(),
            'pending_payment' => $this->registrations()->where('payment_status', 'pending')->count(),
            'revenue' => $this->total_revenue,
            'expected_revenue' => $this->expected_revenue
        ];
    }

    public function getTicketTypeStats()
    {
        return $this->ticketTypes()->with('registrations')->get()->map(function ($ticketType) {
            return [
                'ticket_type' => $ticketType->ticket_name,
                'price' => $ticketType->price,
                'sold' => $ticketType->registrations()->count(),
                'revenue' => $ticketType->registrations()->where('payment_status', 'paid')->sum('amount_paid'),
                'available' => $ticketType->available_quantity
            ];
        });
    }
}
