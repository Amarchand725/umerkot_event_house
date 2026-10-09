<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Event\Http\Controllers\EventController;

// 🧩 Event Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(EventController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{event}/restore', 'restore')->name('restore');
        Route::delete('{event}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EventController::class)
            ->parameters(['' => 'event']);
});
