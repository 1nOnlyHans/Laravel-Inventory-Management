<?php

namespace App\Observers;

use App\Models\Brand;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class BrandObserver
{
    /**
     * Handle the Brand "created" event.
     */
    public function created(Brand $brand): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created a brand',
            'details' => "Brand '{$brand->brand_name}' has been added by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Brand "updated" event.
     */
    public function updated(Brand $brand): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated a brand',
            'details' => "Brand '{$brand->brand_name}' was updated by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted a brand',
            'details' => "Brand '{$brand->brand_name}' has been deleted by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Brand "restored" event.
     */
    public function restored(Brand $brand): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Restored a brand',
            'details' => "Brand '{$brand->brand_name}' has been restored by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Brand "force deleted" event.
     */
    public function forceDeleted(Brand $brand): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Permanently deleted a brand',
            'details' => "Brand '{$brand->brand_name}' was permanently removed by " . Auth::user()->name,
        ]);
    }
}
