<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EmployeeAttendance\Http\Controllers\EmployeeAttendanceController;

// 🧩 EmployeeAttendance Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:employee_attendance']
], function () {
    Route::controller(EmployeeAttendanceController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{employeeAttendance}/restore', 'restore')->name('restore');
        Route::delete('{employeeAttendance}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EmployeeAttendanceController::class)
            ->parameters(['' => 'employeeAttendance']);
});