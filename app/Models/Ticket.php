<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
     protected $fillable = ['nom', 'event', 'code', 'n_billet','n_billet_reel','conctat', 'used', 'vip'];
}
