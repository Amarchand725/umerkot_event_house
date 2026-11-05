<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

// 🧩 ActivityLog Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:report']
], function () {
    Route::controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('/events', 'events')->name('events');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/payments', 'payments')->name('payments');
        Route::get('/settlements', 'settlements')->name('settlements');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/customers', 'customers')->name('customers');
    });
});
