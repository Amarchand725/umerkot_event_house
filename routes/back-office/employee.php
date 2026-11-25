<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Employee\Http\Controllers\EmployeeController;

// 🧩 Employee Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(EmployeeController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{employee}/restore', 'restore')->name('restore');
        Route::delete('{employee}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EmployeeController::class)
            ->parameters(['' => 'employee']);
});