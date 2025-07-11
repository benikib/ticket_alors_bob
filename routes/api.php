<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;




Route::post('/tickets', [TicketController::class, 'store']);
Route::post('/tickets/verify', [TicketController::class, 'verify']);
Route::get('/achats',[TicketController::class, 'index']);
