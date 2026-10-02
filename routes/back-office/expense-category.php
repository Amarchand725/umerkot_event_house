<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ExpenseCategory\Http\Controllers\ExpenseCategoryController;

// 🧩 ExpenseCategory Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(ExpenseCategoryController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{expenseCategory}/restore', 'restore')->name('restore');
        Route::delete('{expenseCategory}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', ExpenseCategoryController::class)
            ->parameters(['' => 'expenseCategory']);
});