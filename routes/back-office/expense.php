<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Expense\Http\Controllers\ExpenseController;

// 🧩 Expense Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(ExpenseController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{expense}/restore', 'restore')->name('restore');
        Route::delete('{expense}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', ExpenseController::class)
            ->parameters(['' => 'expense']);
});