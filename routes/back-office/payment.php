<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payment\Http\Controllers\PaymentController;

// 🧩 Payment Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{payment}/restore', 'restore')->name('restore');
        Route::delete('{payment}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', PaymentController::class)
            ->parameters(['' => 'payment']);
});
