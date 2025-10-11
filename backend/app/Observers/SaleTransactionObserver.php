<?php

namespace App\Observers;

use App\Events\Sold;
use App\Models\AuditLog;
use App\Models\SaleTransaction;
use Illuminate\Support\Facades\Auth;

class SaleTransactionObserver
{
    /**
     * Handle the SaleTransaction "created" event.
     */
    public function created(SaleTransaction $saleTransaction): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created Sale Transaction',
            'details' => 'A new sale transaction was recorded for customer: '
                . ($saleTransaction->customer->name ?? 'Unknown Customer')
                . ' with total amount of ₱' . number_format($saleTransaction->total_amount, 2)
                . ' and reference number: ' . $saleTransaction->reference_no,
        ]);

        broadcast(new Sold($saleTransaction))->toOthers();
    }

    /**
     * Handle the SaleTransaction "updated" event.
     */
    public function updated(SaleTransaction $saleTransaction): void
    {
        //
    }

    /**
     * Handle the SaleTransaction "deleted" event.
     */
    public function deleted(SaleTransaction $saleTransaction): void
    {
        //
    }

    /**
     * Handle the SaleTransaction "restored" event.
     */
    public function restored(SaleTransaction $saleTransaction): void
    {
        //
    }

    /**
     * Handle the SaleTransaction "force deleted" event.
     */
    public function forceDeleted(SaleTransaction $saleTransaction): void
    {
        //
    }
}
