<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PaymentTransaction extends Model
{
    use HasFactory;
    
    protected $connection = 'tenant';
    
    protected $fillable = [
        'registration_id',
        'transaction_reference',
        'external_reference',
        'amount',
        'currency',
        'fees',
        'payment_method',
        'payment_provider',
        'status',
        'payment_date',
        'processed_date',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'payment_date' => 'datetime',
        'processed_date' => 'datetime',
        'metadata' => 'array'
    ];

    // Relations
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Accesseurs
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->fees;
    }

    // Méthodes utilitaires
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'processed_date' => now()
        ]);
        
        // Mettre à jour l'inscription
        if ($this->registration) {
            $this->registration->addPayment($this->amount, $this->transaction_reference);
        }
    }

    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
            'processed_date' => now()
        ]);
    }

    public static function generateReference()
    {
        do {
            $reference = 'TXN' . date('YmdHis') . mt_rand(100, 999);
        } while (static::where('transaction_reference', $reference)->exists());
        
        return $reference;
    }
}
