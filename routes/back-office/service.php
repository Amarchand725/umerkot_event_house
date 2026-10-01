<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Service\Http\Controllers\ServiceController;

// 🧩 Service Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(ServiceController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{service}/restore', 'restore')->name('restore');
        Route::delete('{service}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', ServiceController::class)
            ->parameters(['' => 'service']);
});