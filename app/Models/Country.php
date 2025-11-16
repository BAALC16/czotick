<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    
    protected $table = 'countries';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'code',
        'nom',
        'prefix_telephone',
        'drapeau'
    ];

    // Relations
    public function users()
    {
        return $this->hasMany(User::class, 'code_pays', 'code');
    }

    // Scopes
    public function scopeOrderedByName($query)
    {
        return $query->orderBy('nom');
    }
}

