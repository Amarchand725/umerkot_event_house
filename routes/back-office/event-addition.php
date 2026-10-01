<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EventAddition\Http\Controllers\EventAdditionController;

// 🧩 EventAddition Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(EventAdditionController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{eventAddition}/restore', 'restore')->name('restore');
        Route::delete('{eventAddition}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EventAdditionController::class)
            ->parameters(['' => 'eventAddition']);
});