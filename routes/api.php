<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\BilletController;



Route::post('/billets', [BilletController::class, 'store']);
