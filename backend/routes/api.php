<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaymongoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Mail\PurchaseOrder;
use App\Models\Brand;
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

//Employee Controller AND LOGIC
Route::middleware(['auth:sanctum', 'admin'])->controller(EmployeeController::class)->group(function () {
    Route::get('/employees/index', 'index');
    Route::post('/employees/store', 'store');
    Route::get('/employees/show/{employee_id}', 'show');
    Route::put('/employees/update', 'update');
    Route::put('/employees/updateAccount', 'updateAccount');
});

//PRODUCT LOGIC
Route::middleware(['auth:sanctum', 'admin'])->controller(ProductController::class)->group(function () {
    Route::get('/products/index', 'index');
    Route::get('/products/show/{product_id}', 'show');
    Route::post('/products/productSupplier', 'getProductsBySupplier');
    Route::post('/products/store', 'store');
    Route::put('/products/update', 'update');
    Route::delete('/products/softDelete', 'softDelete');
    Route::delete('/products/deletePhoto', 'deleteProductPhoto');
});

//SUPPLIER AND CATEGORIES CRUD
Route::middleware(['auth:sanctum', 'admin'])->controller(SupplierController::class)->group(function () {
    Route::get('/suppliers/index', 'index');
    Route::post('/suppliers/store', 'store');
    Route::get('/suppliers/show', 'show');
    Route::put('/suppliers/update', 'update');
    Route::delete('/suppliers/destroy', 'destroy');
});

Route::middleware(['auth:sanctum', 'admin'])->controller(CategoryController::class)->group(function () {
    Route::get('/categories/index', 'index');
    Route::post('/categories/store', 'store');
    Route::get('/categories/show', 'show');
    Route::put('/categories/update', 'update');
    Route::delete('/categories/destroy', 'destroy');
});

Route::middleware(['auth:sanctum', 'admin'])->controller(BrandController::class)->group(function () {
    Route::get('/brands/index', 'index');
    Route::get('/brands/show/{brand_id}', 'show');
    Route::post('/brands/store', 'store');
    Route::put('/brands/update', 'update');
    Route::delete('/brands/destroy', 'destroy');
});

Route::middleware(['auth:sanctum', 'admin'])->controller(PurchaseController::class)->group(function () {
    Route::get('/purchase_order/index', 'index');
    Route::get('/purchase/show/{purchase_id}', 'show');
    Route::post('/purchase_order/sendMail', 'mail');
    Route::post('/purchase_order/store', 'store');
    Route::post('/purchase/record', 'createPaymentRecord');
    Route::put('/purchase/updatestatus', 'updateStatus');
    Route::put('/purchase/delivered', 'markAsDelivered');
});

Route::middleware(['auth:sanctum', 'admin'])->controller(PaymongoController::class)->group(function () {
    Route::post('/paymongo/payment', 'createPaymentIntent');
    Route::post('/paymongo/storeSession', 'storeSession');
    Route::get('/paymongo/latest', 'getLatestSession');
    Route::post('/paymongo/checktransaction', 'checkTransactionStatus');
});

Route::middleware(['auth:sanctum', 'admin'])->controller(ProductStockController::class)->group(function () {
    Route::post('/stocks/stockin', 'stockIn');
});
