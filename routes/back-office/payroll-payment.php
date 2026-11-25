<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PayrollPayment\Http\Controllers\PayrollPaymentController;

// 🧩 PayrollPayment Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(PayrollPaymentController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{payrollPayment}/restore', 'restore')->name('restore');
        Route::delete('{payrollPayment}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', PayrollPaymentController::class)
            ->parameters(['' => 'payrollPayment']);
});