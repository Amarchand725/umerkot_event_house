<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Package\Http\Controllers\PackageController;

// 🧩 Package Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(PackageController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{package}/restore', 'restore')->name('restore');
        Route::delete('{package}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', PackageController::class)
            ->parameters(['' => 'package']);
});