<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\TicketController;
use App\Http\Controllers\BilletController;
use App\Services\MpesaService;

use App\Http\Controllers\api\mobileMoneyController;
use App\Http\Controllers\AuthController;


Route::get('mobileMoney/send', [mobileMoneyController::class, 'sendPayment'])->name("mobileMoney.send");

Route::middleware('auth')->group(function () {
    Route::get('billet/', [BilletController::class, 'index'])->name("billet.index");
    Route::post('billet/', [BilletController::class, 'store'])->name("billet.store");
    Route::get('billet/scanne', [BilletController::class, 'scanne'])->name("billet.scanne");
    Route::post('billet/verify', [BilletController::class, 'verify'])->name("billet.verify");

    Route::post('logout', [AuthController::class, 'logout'])->name("login.logout");
    Route::get('/', [BilletController::class, 'index']);
});


Route::get('login', [AuthController::class, 'login'])->name("login");
Route::post('login', [AuthController::class, 'toLogin'])->name("login.toLogin");





