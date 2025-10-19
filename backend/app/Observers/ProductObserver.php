<?php

namespace App\Observers;

use App\Events\LowStock;
use App\Events\newProduct;
use App\Events\OutOfStock;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockAlert;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //
        broadcast(new newProduct($product))->toOthers();
        if ($product->status === 'Low Stock') {
            broadcast(new LowStock($product))->toOthers();
        } else if ($product->status === 'Out of Stock') {
            broadcast(new OutOfStock($product))->toOthers();
        }

        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Added new Product',
            'details' => $product->product_name . ' has been added'
        ]);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
        if ($product->status === 'Low Stock') {
            broadcast(new LowStock($product))->toOthers();
        } else if ($product->status === 'Out of Stock') {
            broadcast(new OutOfStock($product))->toOthers();
        }

        if ($product->product_quantity > $product->reorder_level) {
            foreach ($product->alerts as $alert) {
                $alert->status = 'Resolved';
                $alert->save();
            }
        }

        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Updated a Product',
            'details' => $product->product_name . ' has been updated'
        ]);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Removed a Product',
            'details' => $product->product_name . ' has been removed'
        ]);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Restored a Product',
            'details' => $product->product_name . ' has been removed'
        ]);
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //        
        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Permanently deleted a product',
            'details' => $product->product_name . ' has been permanently removed'
        ]);
    }
}
