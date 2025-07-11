<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    
    public function index(){
      
        $tickets = Ticket::all();
        return View("ticket.index",["tickets"=>$tickets]);
        
     }

     public function scanne(){
        return View("ticket.scanner");
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
