<?php

use Illuminate\Support\Facades\Route;
use App\Modules\InventoryCategory\Http\Controllers\InventoryCategoryController;

// 🧩 InventoryCategory Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(InventoryCategoryController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{inventoryCategory}/restore', 'restore')->name('restore');
        Route::delete('{inventoryCategory}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', InventoryCategoryController::class)
            ->parameters(['' => 'inventoryCategory']);
});