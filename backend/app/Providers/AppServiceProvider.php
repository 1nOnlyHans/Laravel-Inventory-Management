<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Observers\BrandObserver;
use App\Observers\CategoryObserver;
use App\Observers\EmployeeObserver;
use App\Observers\ProductObserver;
use App\Observers\PurchaseObserver;
use App\Observers\StockMovementObserver;
use App\Observers\SupplierObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Employee::observe(EmployeeObserver::class);
        User::observe(UserObserver::class);
        Product::observe(ProductObserver::class);
        Purchase::observe(PurchaseObserver::class);
        Supplier::observe(SupplierObserver::class);
        Category::observe(CategoryObserver::class);
        Brand::observe(BrandObserver::class);
        StockMovement::observe(StockMovementObserver::class);
    }
}
