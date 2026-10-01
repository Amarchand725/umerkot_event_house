<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PaymentMethod\Http\Controllers\PaymentMethodController;

// 🧩 PaymentMethod Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(PaymentMethodController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{paymentMethod}/restore', 'restore')->name('restore');
        Route::delete('{paymentMethod}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', PaymentMethodController::class)
            ->parameters(['' => 'paymentMethod']);
});