<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index(){
        $ticket = Ticket::all();
         return response()->json($ticket);
    }
     public function store(Request $request)
    {
        $ticket = Ticket::create([
            'nom' => $request->nom,
            'conctat' => $request->conctat,
            'n_billet' => $request->n_billet,
            'code' => 'BILLET-' . strtoupper(Str::random(10)),
        ]);

        return response()->json($ticket);
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
