<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql';
    
    protected $fillable = [
        'organization_id',
        'invoice_number',
        'subtotal',
        'tax_amount',
        'total_amount',
        'billing_period_start',
        'billing_period_end',
        'status',
        'due_date',
        'paid_at',
        'items'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'items' => 'array'
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')
                    ->where('due_date', '<', now());
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Accesseurs
    public function getIsOverdueAttribute()
    {
        return $this->status === 'sent' && $this->due_date < now();
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    // Méthodes utilitaires
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $lastInvoice = self::whereYear('created_at', $year)
                          ->orderBy('id', 'desc')
                          ->first();
        
        $sequence = $lastInvoice ? 
            intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return 'INV-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}