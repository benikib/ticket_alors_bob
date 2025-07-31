<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\TicketController;
use App\Http\Controllers\BilletController;
use App\Services\MpesaService;

use App\Http\Controllers\api\mobileMoneyController;



Route::get('mobileMoney/send', [mobileMoneyController::class, 'sendPayment'])->name("mobileMoney.send");

Route::get('/', [BilletController::class, 'index'])->name("billet.index");
Route::get('billet/', [BilletController::class, 'index'])->name("billet.index");
Route::post('billet/', [BilletController::class, 'store'])->name("billet.store");
Route::get('billet/scanne', [BilletController::class, 'scanne'])->name("billet.scanne");
Route::post('billet/verify', [BilletController::class, 'verify'])->name("billet.verify");





