<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Str;

class TicketController extends Controller
{
    public function index(){
       try {
            $tickets = Ticket::all();
            return response()->json($tickets);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
   public function store(Request $request)
{
    try {
        $rawCode = 'BILLET-' . strtoupper(Str::random(10));

        $ticket = Ticket::create([
            'nom' => $request->nom,
            'conctat' => $request->conctat,
            'n_billet' => $request->n_billet,
            'vip' => $request->vip ?? false,
            'code' => Hash::make($rawCode), // stocke le hash
        ]);

        return response()->json([
            'code' => $rawCode // retourne le code lisible au client
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}




public function verify(Request $request)
{
    $tickets = Ticket::where('used', false)->get();

    foreach ($tickets as $ticket) {
        if (Hash::check($request->code, $ticket->code)) {
            if ($ticket->n_billet > 1) {
                // On décrémente le nombre de billets restants
                $ticket->decrement('n_billet');
            } else {
                // C'est le dernier billet, on désactive le ticket
                $ticket->update([
                    'used' => true,
                    'n_billet' => 0
                ]);
            }

            return response()->json([
                  'valid' => true,
                'nom' => $ticket->nom,
                'conctat' => $ticket->conctat,
                'n_billet' => $ticket->n_billet,
                'vip' => $ticket->vip,
                'message' => $ticket->n_billet === 0 ? 'Dernier billet utilisé' : 'Billet validé'
            ]);
        }
    }

    return response()->json([
           'valid' => false,
        'message' => 'Code invalide ou déjà utilisé'
    ]);
}

}
