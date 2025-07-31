<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

class BilletController extends Controller
{
    //

    public function store(Request $request)
    {
        try {
            $now = time(); 
            $start1978 = strtotime('1978-01-01 00:00:00'); 
            $secondsSince1978 = $now - $start1978;
            $rawCode = 'Ticket-' . $request->nom . '-' . $secondsSince1978 . '-' . uniqid();
            $rawCode=Hash::make($rawCode);
            $ticket = Ticket::create([
                'nom'      => $request->nom,
                'conctat'  => $request->conctat,
                'n_billet' => $request->n_billet,
                'n_billet_reel' => $request->n_billet,
                'vip'      => $request->vip, 
                'code'     => $rawCode,
            ]);
    
            return response()->json([
                'code' => $rawCode 
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
