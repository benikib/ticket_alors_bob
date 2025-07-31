<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\TicketController;
use App\Http\Controllers\BilletController;
use App\Services\MpesaService;



Route::get('tickets/', [TicketController::class, 'index'])->name("ticket.index");
Route::post('tickets/', [TicketController::class, 'store'])->name("ticket.store");
Route::get('/ticket/scanne', [TicketController::class, 'scanne'])->name("ticket.scanne");
Route::post('/ticket/verify', [TicketController::class, 'verify'])->name("ticket.verify");

Route::get('/', [BilletController::class, 'index'])->name("billet.index");
Route::get('billet/', [BilletController::class, 'index'])->name("billet.index");
Route::post('billet/', [BilletController::class, 'store'])->name("billet.store");
Route::get('billet/scanne', [BilletController::class, 'scanne'])->name("billet.scanne");
Route::post('billet/verify', [BilletController::class, 'verify'])->name("billet.verify");





