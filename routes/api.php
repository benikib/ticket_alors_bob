<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\BilletController;

use App\Http\Controllers\api\mobileMoneyController;



Route::post('/billets', [BilletController::class, 'store']);

Route::post('mobileMoney/send', [mobileMoneyController::class, 'sendPayment'])
->name("mobileMoney.send");
