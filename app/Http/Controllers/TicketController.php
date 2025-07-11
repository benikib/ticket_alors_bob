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
            
            $now = time(); 
            $start1978 = strtotime('1978-01-01 00:00:00'); 
            $secondsSince1978 = $now - $start1978;
            $code = 'Ticket-' . $secondsSince1978;
    
            $ticket = Ticket::create([
                'nom'      => $request->nom,
                'conctat'  => $request->conctat,
                'n_billet' => $request->n_billet,
                'vip'      => $request->vip, 
                'code'     => $code,
            ]);
    
            return response()->json([
                'code' => $ticket->code
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request)
    {
        $ticket = Ticket::where('code', $request->code)->first();

        if (!$ticket) {
            return response()->json(['valid' => false, 'message' => 'Invalide']);
        }

        if ($ticket->used) {
            return response()->json(['valid' => false, 'message' => 'Déjà utilisé']);
        }

        $ticket->update(['used' => true]);

        return response()->json([
            'valid' => true,
            'nom' => $ticket->nom,
            'conctat' => $ticket->conctat,
            'conctat' => $request->conctat,
            'n_billet' => $request->n_billet,
        ]);
    }
}
