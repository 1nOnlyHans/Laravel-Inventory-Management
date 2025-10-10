<?php

namespace App\Observers;

use App\Models\Supplier;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SupplierObserver
{
    /**
     * Handle the Supplier "created" event.
     */
    public function created(Supplier $supplier): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created a supplier',
            'details' => "Supplier '{$supplier->supplier_name}' has been added by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Supplier "updated" event.
     */
    public function updated(Supplier $supplier): void
    {

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated a supplier',
            'details' => "Supplier '{$supplier->supplier_name}' was updated by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Supplier "deleted" event.
     */
    public function deleted(Supplier $supplier): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted a supplier',
            'details' => "Supplier '{$supplier->supplier_name}' has been deleted by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Supplier "restored" event.
     */
    public function restored(Supplier $supplier): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Restored a supplier',
            'details' => "Supplier '{$supplier->supplier_name}' has been restored by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Supplier "force deleted" event.
     */
    public function forceDeleted(Supplier $supplier): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Permanently deleted a supplier',
            'details' => "Supplier '{$supplier->supplier_name}' was permanently removed by " . Auth::user()->name,
        ]);
    }
}
