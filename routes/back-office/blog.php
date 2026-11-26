<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Blog\Http\Controllers\BlogController;

// 🧩 Blog Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(BlogController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{blog}/restore', 'restore')->name('restore');
        Route::delete('{blog}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', BlogController::class)
            ->parameters(['' => 'blog']);
});