<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Review\Http\Controllers\ReviewController;

// 🧩 Review Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:review']
], function () {
    Route::controller(ReviewController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{review}/restore', 'restore')->name('restore');
        Route::delete('{review}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', ReviewController::class)
            ->parameters(['' => 'review']);
});
