<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Event\Http\Controllers\EventController;

// 🧩 Event Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(EventController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{event}/restore', 'restore')->name('restore');
        Route::delete('{event}/force-delete', 'forceDelete')->name('forceDelete');

        Route::get('action/edit/{event}/status', 'status')->name('action.status');
        Route::post('{event}/update-status', 'updateStatus')->name('update-status');

        Route::get('action/edit/{event}/payment', 'payment')->name('action.payment');
        Route::post('{event}/store-payment', 'storePayment')->name('store-payment');

        Route::get('action/edit/{event}/order', 'order')->name('action.order');
        Route::post('{event}/store-order', 'storeOrder')->name('store-order');

        Route::get('action/edit/{event}/return-order', 'returnOrder')->name('action.return-order');
        Route::post('{event}/store-return-order', 'storeReturnOrder')->name('store-return-order');
    });

    // 🧱 Resource CRUD
    Route::resource('/', EventController::class)
            ->parameters(['' => 'event']);
});
