<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Registered a new account',
            'details' => 'System account for ' . $user->employee->unique_employee_id . ' has been created'
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => 'Updated an account',
            'details' => 'System account for ' . $user->employee->unique_employee_id . ' has been updated'
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
