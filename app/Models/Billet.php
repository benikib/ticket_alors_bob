<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billet extends Model
{
    
    //
    protected $fillable = 
    [
        'nom_complet_client', 
        'numero_client',
        'numero_billet',
         'code_bilet', 
        'occurance_billet',
        'nombre_reel',
        'moyen_achat',
        'statut_billet',
         'tarif_id',
         'type_billet_id'
        ];

    public function typeBillet()
    {
        return $this->belongsTo(Type_billet::class, 'type_billet_id');
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'tarif_id');
    }
}
