<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\TicketController;
use App\Services\MpesaService;


Route::get('/', [TicketController::class, 'index'])->name("ticket.index");
Route::get('tickets/', [TicketController::class, 'index'])->name("ticket.index");
Route::post('tickets/', [TicketController::class, 'store'])->name("ticket.store");
Route::get('/ticket/scanne', [TicketController::class, 'scanne'])->name("ticket.scanne");
Route::post('/ticket/verify', [TicketController::class, 'verify'])->name("ticket.verify");




Route::get('/mpesa-session', function (MpesaService $mpesa) {
    try {
        $session = $mpesa->getSession();
        return response()->json($session);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});


