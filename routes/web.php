<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\TicketController;


Route::get('/', [TicketController::class, 'index'])->name("ticket.index");
Route::get('tickets/', [TicketController::class, 'index'])->name("ticket.index");
Route::get('/ticket/scanne', [TicketController::class, 'scanne'])->name("ticket.scanne");
Route::post('/ticket/verify', [TicketController::class, 'verify'])->name("ticket.verify");



