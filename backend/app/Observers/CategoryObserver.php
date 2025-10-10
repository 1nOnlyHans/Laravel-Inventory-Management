<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created a category',
            'details' => "Category '{$category->category_name}' has been added by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated a category',
            'details' => "Category '{$category->category_name}' was updated by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted a category',
            'details' => "Category '{$category->category_name}' has been deleted by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Restored a category',
            'details' => "Category '{$category->category_name}' has been restored by " . Auth::user()->name,
        ]);
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Permanently deleted a category',
            'details' => "Category '{$category->category_name}' was permanently removed by " . Auth::user()->name,
        ]);
    }
}
