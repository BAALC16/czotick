<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationLog extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'jci_verification_logs';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'verifier_id',
        'convention_id',
        'event_type',
        'verification_time',
        'status',
        'ip_address',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verification_time' => 'datetime',
    ];

    /**
     * Indique si le modèle doit être horodaté.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Obtenir le vérificateur associé à ce journal.
     */
    public function verifier()
    {
        return $this->belongsTo(Verifier::class, 'verifier_id');
    }

    /**
     * Obtenir l'inscription associée à ce journal.
     */
    public function convention()
    {
        return $this->belongsTo(Convention::class, 'convention_id');
    }
}