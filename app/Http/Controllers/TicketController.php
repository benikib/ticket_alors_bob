<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
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
            'code' => $rawCode // retourne le code lisible au client
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}


public function verify(Request $request)
{
    // On cherche directement un ticket non utilisé avec le code haché
    $ticket = Ticket::where('used', false)
                    ->where('code', $request->code)
                    ->first();

    // Si aucun ticket ne correspond
    if (!$ticket) {
        return response()->json([
            'valid' => false,
            'message' => 'Code invalide'
        ], 404);
    }

    // Traitement selon le nombre de billets restants
    if ($ticket->n_billet > 1) {
        $ticket->decrement('n_billet');
        $message = 'Billet validé';
    } else {
        $ticket->update([
            'used' => true,
            'n_billet' => 0
        ]);
        $message = 'Dernier billet utilisé';
    }

    // Réponse
    return response()->json([
        'valid' => true,
        'nom' => $ticket->nom,
        'conctat' => $ticket->conctat,
        'n_billet' => $ticket->n_billet,
        'vip' => $ticket->vip,
        'message' => $message
    ]);
}

}
