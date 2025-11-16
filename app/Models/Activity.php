<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable =[
        'title',
        'content',
        'image',
        'program_id',
        'senateur',
        'membre',
        'etudiant',
        'dateStart',
        'dateEnd',

    ];

    public function owner()
    {
      return $this->belongsTo(User::class, 'user_id');
    }

}

