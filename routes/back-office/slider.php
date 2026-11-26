<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Slider\Http\Controllers\SliderController;

// 🧩 Slider Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(SliderController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{slider}/restore', 'restore')->name('restore');
        Route::delete('{slider}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', SliderController::class)
            ->parameters(['' => 'slider']);
});