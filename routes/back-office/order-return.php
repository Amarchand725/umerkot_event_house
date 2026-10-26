<?php

use Illuminate\Support\Facades\Route;
use App\Modules\OrderReturn\Http\Controllers\OrderReturnController;

// 🧩 OrderReturn Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(OrderReturnController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{orderReturn}/restore', 'restore')->name('restore');
        Route::delete('{orderReturn}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', OrderReturnController::class)
            ->parameters(['' => 'orderReturn']);
});