<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EmployeeAdvance\Http\Controllers\EmployeeAdvanceController;

// 🧩 EmployeeAdvance Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:employee_advance']
], function () {
    Route::controller(EmployeeAdvanceController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{employeeAdvance}/restore', 'restore')->name('restore');
        Route::delete('{employeeAdvance}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EmployeeAdvanceController::class)
            ->parameters(['' => 'employeeAdvance']);
});