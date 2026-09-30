<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Unit\Http\Controllers\UnitController;

// 🧩 Unit Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(UnitController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{unit}/restore', 'restore')->name('restore');
        Route::delete('{unit}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', UnitController::class)
            ->parameters(['' => 'unit']);
});