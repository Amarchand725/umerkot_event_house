<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EmployeeCompensation\Http\Controllers\EmployeeCompensationController;

// 🧩 EmployeeCompensation Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:employee_compensation']
], function () {
    Route::controller(EmployeeCompensationController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{employeeCompensation}/restore', 'restore')->name('restore');
        Route::delete('{employeeCompensation}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EmployeeCompensationController::class)
            ->parameters(['' => 'employeeCompensation']);
});