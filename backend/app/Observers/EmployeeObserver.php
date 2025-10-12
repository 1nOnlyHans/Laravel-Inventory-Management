<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::user()->id,
            'action' => "Registered new User",
            'details' => $employee->unique_employee_id . ' has been registered'
        ]);
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated employee record',
            'details' => "{$employee->unique_employee_id} profile was updated by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        //
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted an employee',
            'details' => "{$employee->unique_employee_id} profile was deleted by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }
}
