<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payroll\Http\Controllers\PayrollController;

// 🧩 Payroll Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(PayrollController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{payroll}/restore', 'restore')->name('restore');
        Route::delete('{payroll}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', PayrollController::class)
            ->parameters(['' => 'payroll']);
});