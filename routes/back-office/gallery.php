<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Gallery\Http\Controllers\GalleryController;

// 🧩 Gallery Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(GalleryController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{gallery}/restore', 'restore')->name('restore');
        Route::delete('{gallery}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', GalleryController::class)
            ->parameters(['' => 'gallery']);
});