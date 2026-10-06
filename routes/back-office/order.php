<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Order\Http\Controllers\OrderController;

// 🧩 Order Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(OrderController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{order}/restore', 'restore')->name('restore');
        Route::delete('{order}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', OrderController::class)
            ->parameters(['' => 'order']);
});