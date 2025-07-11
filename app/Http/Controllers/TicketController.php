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

        try{
             $ticket = Ticket::create([
            'nom' => $request->nom,
            'conctat' => $request->conctat,
            'n_billet' => $request->n_billet,
            'vip' =>$request->vip ?? false,
            'code' => 'BILLET-' . strtoupper(Str::random(10)),
        ]);

        return response()->json([
            'code' => $ticket->code
        ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
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
