<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Employee\Http\Controllers\EmployeeController;

// 🧩 Employee Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:employee']
], function () {
    Route::controller(EmployeeController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{employee}/restore', 'restore')->name('restore');
        Route::delete('{employee}/force-delete', 'forceDelete')->name('forceDelete');

        Route::get('action/edit/{employee}/status', 'status')->name('action.status');
        Route::post('{employee}/update-status', 'updateStatus')->name('update-status');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EmployeeController::class)
            ->parameters(['' => 'employee']);
});
