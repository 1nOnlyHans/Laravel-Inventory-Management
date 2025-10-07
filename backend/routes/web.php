<?php

use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::view('/test', 'mail.purchase-order');


Route::get('/purchase/accept/{purchase}', [PurchaseController::class, 'acceptOrder']);
