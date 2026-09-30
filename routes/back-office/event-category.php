<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EventCategory\Http\Controllers\EventCategoryController;

// 🧩 EventCategory Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(EventCategoryController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{eventCategory}/restore', 'restore')->name('restore');
        Route::delete('{eventCategory}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EventCategoryController::class)
            ->parameters(['' => 'eventCategory']);
});