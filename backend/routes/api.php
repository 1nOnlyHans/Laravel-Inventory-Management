<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//Authentication
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('/getAuthUser', 'getAuthUser')->middleware('auth:sanctum');
});

// Route::group(['middleware' => ['auth:sanctum']], function () {});

//Employee Controller
Route::middleware(['auth:sanctum', 'admin'])->controller(EmployeeController::class)->group(function () {
    Route::get('/employees/index', 'index');
    Route::post('/employees/store', 'store');
    Route::get('/employees/show/{employee_id}', 'show');
    Route::put('/employees/update', 'update');
    Route::put('/employees/updateAccount', 'updateAccount');
});
