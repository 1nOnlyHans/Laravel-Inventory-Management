<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Created a new Purchase Order Request',
            'details' => 'A new purchase order request has been sent to ' . $purchase->supplier->supplier_name
        ]);
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "deleted" event.
     */
    public function deleted(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "restored" event.
     */
    public function restored(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "force deleted" event.
     */
    public function forceDeleted(Purchase $purchase): void
    {
        //
    }
}
