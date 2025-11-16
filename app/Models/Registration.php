<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Registration extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'registration_number',
        'fullname',
        'phone',
        'email',
        'organization',
        'position',
        'dietary_requirements',
        'special_needs',
        'question_1',
        'question_2',
        'question_3',
        'status',
        'payment_status',
        'ticket_price',
        'amount_paid',
        'used_opening',
        'used_conference',
        'used_networking',
        'used_photos',
        'confirmation_date',
        'cancellation_date'
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'registration_date' => 'datetime',
        'confirmation_date' => 'datetime',
        'cancellation_date' => 'datetime',
        'used_opening' => 'boolean',
        'used_conference' => 'boolean',
        'used_networking' => 'boolean',
        'used_photos' => 'boolean'
    ];

    // Relations
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    // Accesseurs
    public function getBalanceDueAttribute()
    {
        return max(0, $this->ticket_price - $this->amount_paid);
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->amount_paid >= $this->ticket_price;
    }

    public function getIsPartiallyPaidAttribute()
    {
        return $this->amount_paid > 0 && $this->amount_paid < $this->ticket_price;
    }

    public function getPaymentProgressAttribute()
    {
        if ($this->ticket_price == 0) {
            return 100;
        }

        return round(($this->amount_paid / $this->ticket_price) * 100, 1);
    }

    // Boot method pour auto-générer le numéro
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            if (!$registration->registration_number) {
                $registration->registration_number = static::generateRegistrationNumber();
            }
        });
    }

    public static function generateRegistrationNumber()
    {
        do {
            $number = 'Czotick' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('registration_number', $number)->exists());

        return $number;
    }

    // Méthodes utilitaires
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmation_date' => now()
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_date' => now()
        ]);
    }

    public function addPayment($amount, $transactionReference = null)
    {
        $newAmountPaid = $this->amount_paid + $amount;

        $this->update([
            'amount_paid' => $newAmountPaid,
            'payment_status' => $newAmountPaid >= $this->ticket_price ? 'paid' : 'partial'
        ]);

        // Si entièrement payé et pas encore confirmé, confirmer automatiquement
        if ($this->is_fully_paid && $this->status === 'pending') {
            $this->confirm();
        }
    }

    public function markZoneAsUsed($zone)
    {
        $field = "used_{$zone}";
        if (in_array($field, $this->fillable)) {
            $this->update([$field => true]);
        }
    }

    public function hasUsedZone($zone)
    {
        $field = "used_{$zone}";
        return $this->$field ?? false;
    }
}
