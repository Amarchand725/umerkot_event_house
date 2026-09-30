<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Customer\Http\Controllers\CustomerController;

// 🧩 Customer Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{customer}/restore', 'restore')->name('restore');
        Route::delete('{customer}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', CustomerController::class)
            ->parameters(['' => 'customer']);
});
