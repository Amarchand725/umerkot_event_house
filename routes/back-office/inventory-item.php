<?php

use Illuminate\Support\Facades\Route;
use App\Modules\InventoryItem\Http\Controllers\InventoryItemController;

// 🧩 InventoryItem Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(InventoryItemController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{inventoryItem}/restore', 'restore')->name('restore');
        Route::delete('{inventoryItem}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', InventoryItemController::class)
            ->parameters(['' => 'inventoryItem']);
});