<?php

use Illuminate\Support\Facades\Route;
use App\Modules\FrequentlyAskedQuestion\Http\Controllers\FrequentlyAskedQuestionController;

// 🧩 FrequentlyAskedQuestion Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:frequently_asked_question']
], function () {
    Route::controller(FrequentlyAskedQuestionController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{frequentlyAskedQuestion}/restore', 'restore')->name('restore');
        Route::delete('{frequentlyAskedQuestion}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', FrequentlyAskedQuestionController::class)
            ->parameters(['' => 'frequentlyAskedQuestion']);
});