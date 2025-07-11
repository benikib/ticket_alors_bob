<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/tickets', [TicketController::class, 'store']);
Route::post('/tickets/verify', [TicketController::class, 'verify']);
Route::get('/acahts',[TicketController::class, 'index']);
