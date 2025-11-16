<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;

    protected $table = 'ticket_types';

    protected $fillable = [
        'event_id',
        'ticket_name',
        'ticket_description',
        'ticket_code',
        'price',
        'currency',
        'max_quantity',
        'quantity_sold',
        'sale_start_date',
        'sale_end_date',
        'is_active',
        'requires_membership',
        'membership_organization',
        'min_age',
        'max_age',
        'display_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_quantity' => 'integer',
        'quantity_sold' => 'integer',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'is_active' => 'boolean',
        'requires_membership' => 'boolean',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'quantity_sold' => 0,
        'is_active' => true,
        'requires_membership' => false,
        'display_order' => 0,
    ];

    /**
     * Relation avec l'événement
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation avec les inscriptions
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Scope pour les types de billets actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les types de billets disponibles à la vente
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('sale_start_date')
                          ->orWhere('sale_start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('sale_end_date')
                          ->orWhere('sale_end_date', '>=', now());
                    });
    }

    /**
     * Scope pour ordonner par ordre d'affichage
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }

    /**
     * Vérifier si le type de billet est disponible
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Vérifier les dates de vente
        $now = now();
        if ($this->sale_start_date && $this->sale_start_date > $now) {
            return false;
        }

        if ($this->sale_end_date && $this->sale_end_date < $now) {
            return false;
        }

        // Vérifier la quantité disponible
        if ($this->max_quantity && $this->quantity_sold >= $this->max_quantity) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir le nombre de billets restants
     */
    public function getRemainingQuantityAttribute(): ?int
    {
        if (!$this->max_quantity) {
            return null; // Quantité illimitée
        }

        return max(0, $this->max_quantity - $this->quantity_sold);
    }

    /**
     * Vérifier si les billets sont épuisés
     */
    public function isSoldOut(): bool
    {
        return $this->max_quantity && $this->quantity_sold >= $this->max_quantity;
    }

    /**
     * Obtenir le prix formaté
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' ' . ($this->currency ?? 'FCFA');
    }

    /**
     * Incrémenter la quantité vendue
     */
    public function incrementSold(int $quantity = 1): bool
    {
        if ($this->max_quantity && ($this->quantity_sold + $quantity) > $this->max_quantity) {
            return false;
        }

        $this->increment('quantity_sold', $quantity);
        return true;
    }

    /**
     * Décrémenter la quantité vendue (pour les annulations)
     */
    public function decrementSold(int $quantity = 1): void
    {
        $this->quantity_sold = max(0, $this->quantity_sold - $quantity);
        $this->save();
    }

    /**
     * Vérifier si une personne peut acheter ce type de billet
     */
    public function canBePurchasedBy(?int $age = null, ?string $membershipOrg = null): bool
    {
        // Vérifier l'âge minimum
        if ($this->min_age && $age && $age < $this->min_age) {
            return false;
        }

        // Vérifier l'âge maximum
        if ($this->max_age && $age && $age > $this->max_age) {
            return false;
        }

        // Vérifier l'appartenance requise
        if ($this->requires_membership && $this->membership_organization) {
            if (!$membershipOrg || $membershipOrg !== $this->membership_organization) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtenir le statut de disponibilité sous forme de texte
     */
    public function getAvailabilityStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactif';
        }

        $now = now();
        if ($this->sale_start_date && $this->sale_start_date > $now) {
            return 'Vente pas encore ouverte';
        }

        if ($this->sale_end_date && $this->sale_end_date < $now) {
            return 'Vente fermée';
        }

        if ($this->isSoldOut()) {
            return 'Épuisé';
        }

        return 'Disponible';
    }
}