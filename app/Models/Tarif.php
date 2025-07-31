<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Tarif extends Model
{
    //
        
    //
    protected $fillable = 
    [
        'prix', 
        'devise',
        'type_billet_id'
        ];
}
