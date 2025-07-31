<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type_billet extends Model
{
  
    protected $fillable = 
    [
        'nom_type_billet', 
        'quantite_disponible',
        ];
}
