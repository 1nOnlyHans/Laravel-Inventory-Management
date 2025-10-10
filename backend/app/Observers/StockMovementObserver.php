<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StockMovementObserver
{
    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $stockMovement): void
    {
        //
        $user = Auth::user();
        $product = $stockMovement->product->product_name ?? 'Unknown Product';
        $type = ucfirst($stockMovement->movement_type);
        $quantity = $stockMovement->quantity ?? 0;

        AuditLog::create([
            'user_id' => $user?->id,
            'action' => 'Created a stock movement',
            'details' => "{$user?->name} {$type} {$quantity} units for '{$product}'.",
        ]);
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }
}
