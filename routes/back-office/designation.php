<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Designation\Http\Controllers\DesignationController;

// 🧩 Designation Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(DesignationController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{designation}/restore', 'restore')->name('restore');
        Route::delete('{designation}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', DesignationController::class)
            ->parameters(['' => 'designation']);
});