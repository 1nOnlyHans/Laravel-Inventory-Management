<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaymongoController;
use App\Http\Controllers\PDFcontroller;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemSettingController;
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

Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(AdminDashboardController::class)->group(function () {
    Route::get('/admin/dashboard', 'getDashboardDatas');
});

//Employee Controller AND LOGIC
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(EmployeeController::class)->group(function () {
    Route::get('/employees/index', 'index');
    Route::post('/employees/store', 'store');
    Route::get('/employees/show/{employee_id}', 'show');
    Route::put('/employees/update', 'update');
    Route::put('/employees/updateAccount', 'updateAccount');
    Route::delete('/employee/destroy', 'destroy');
});

//PRODUCT LOGIC
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(ProductController::class)->group(function () {
    Route::get('/products/index', 'index')->withoutMiddleware(['admin']);;
    Route::get('/products/show/{product_id}', 'show')->withoutMiddleware(['admin']);
    Route::get('/products/archived', 'archived');
    Route::post('/products/productSupplier', 'getProductsBySupplier');
    Route::post('/products/store', 'store');
    Route::post('/products/restore', 'restore');
    Route::put('/products/update', 'update');
    Route::delete('/products/softDelete', 'softDelete');
    Route::delete('/products/deletePhoto', 'deleteProductPhoto');
    Route::delete('/products/forceDelete', 'forceDelete');
});

//SUPPLIER, CATEGORIES, AND BRANDS CRUD
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(SupplierController::class)->group(function () {
    Route::get('/suppliers/index', 'index')->withoutMiddleware(['admin']);;
    Route::post('/suppliers/store', 'store');
    Route::get('/suppliers/show', 'show');
    Route::put('/suppliers/update', 'update');
    Route::delete('/suppliers/destroy', 'destroy');
});

Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(CategoryController::class)->group(function () {
    Route::get('/categories/index', 'index')->withoutMiddleware(['admin']);
    Route::post('/categories/store', 'store');
    Route::get('/categories/show', 'show');
    Route::put('/categories/update', 'update');
    Route::delete('/categories/destroy', 'destroy');
});

Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(BrandController::class)->group(function () {
    Route::get('/brands/index', 'index')->withoutMiddleware(['admin']);
    Route::get('/brands/show/{brand_id}', 'show');
    Route::post('/brands/store', 'store');
    Route::put('/brands/update', 'update');
    Route::delete('/brands/destroy', 'destroy');
});

//PURCHASE ORDERS
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(PurchaseController::class)->group(function () {
    Route::get('/purchase_order/index', 'index');
    Route::get('/purchase/show/{purchase_id}', 'show');
    Route::post('/purchase_order/sendMail', 'mail');
    Route::post('/purchase_order/store', 'store');
    Route::post('/purchase/record', 'createPaymentRecord');
    Route::put('/purchase/updatestatus', 'updateStatus');
    Route::put('/purchase/delivered', 'markAsDelivered');
});

// PAYMONGO
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(PaymongoController::class)->group(function () {
    Route::post('/paymongo/payment', 'createPaymentIntent');
    Route::post('/paymongo/storeSession', 'storeSession');
    Route::get('/paymongo/latest', 'getLatestSession');
    Route::post('/paymongo/checktransaction', 'checkTransactionStatus');
});

//STOCK
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(ProductStockController::class)->group(function () {
    Route::get('/stocks/index', 'index');
    Route::post('/stocks/stockin', 'stockIn');
});

//ALERTS
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(StockAlertController::class)->group(function () {
    Route::get('/alerts/index', 'index')->withoutMiddleware(['admin']);
});

//LOGS
Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(AuditLogController::class)->group(function () {
    Route::get('/logs/index', 'index')->withoutMiddleware(['admin']);
});

Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(PDFcontroller::class)->group(function () {
    Route::get('/pdf/inventory', 'generateInventoryValuationReport');
    Route::get('/pdf/movement', 'generateStockMovementsReport');
    Route::get('/pdf/purchase', 'generatePurchaseHistoryReport');
    Route::get('/pdf/sales', 'generateSalesReport');
    Route::get('/pdf/lowStock', 'generateLowStockReport');
    Route::get('/pdf/outOfStock', 'generateOutOfStockReport');
});

Route::middleware(['auth:sanctum', 'staff', 'authenticated'])->controller(StaffController::class)->group(function () {
    Route::get('/staff/getTransactions', 'getSaleTransactions')->withoutMiddleware(['staff']);
    Route::post('/staff/sale', 'finalizedSale');
    Route::get('/staff/dashboard', 'getDashboardDatas');
});

Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(SystemSettingController::class)->group(function () {
    Route::get('/system/index', 'index');
    Route::post('/system/config', 'create');
});

Route::middleware(['auth:sanctum', 'admin', 'authenticated'])->controller(CsvImportController::class)->group(function () {
    Route::post('/csv/staff', 'employeeBulkImport');
});
