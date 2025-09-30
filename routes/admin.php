<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Admin root redirect
Route::get('/admin', function () {
    return redirect()->route('admin.masters.index');
});

// Admin UI routes (Inertia pages)
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('/masters', [\App\Http\Controllers\Admin\MasterController::class, 'index'])->name('admin.masters.index');
    Route::get('/masters/{id}/edit', [\App\Http\Controllers\Admin\MasterController::class, 'edit'])->name('admin.masters.edit');
    Route::get('/import', [\App\Http\Controllers\Admin\ImportController::class, 'index'])->name('admin.import.index');
    Route::get('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/services/{id}/edit', [\App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('admin.services.edit');
});

// Admin JSON API routes
Route::group(['prefix' => 'admin-api', 'middleware' => ['auth', 'api']], function () {
    Route::get('/masters', [\App\Http\Controllers\Admin\MasterController::class, 'list'])->name('admin.api.masters.list');
    Route::get('/masters/{id}', [\App\Http\Controllers\Admin\MasterController::class, 'get'])->name('admin.api.masters.get');
    Route::put('/masters/{id}', [\App\Http\Controllers\Admin\MasterController::class, 'update'])->name('admin.api.masters.update');
    Route::delete('/masters/{id}', [\App\Http\Controllers\Admin\MasterController::class, 'destroy'])->name('admin.api.masters.destroy');
    Route::delete('/masters', [\App\Http\Controllers\Admin\MasterController::class, 'destroyAll'])->name('admin.api.masters.destroy_all');
    Route::get('/services', [\App\Http\Controllers\Admin\MasterController::class, 'services'])->name('admin.api.services');
    Route::get('/masters/{id}/reviews', [\App\Http\Controllers\Admin\MasterController::class, 'reviews'])->name('admin.api.masters.reviews');
    Route::post('/masters/{id}/reviews', [\App\Http\Controllers\Admin\MasterController::class, 'storeReview'])->name('admin.api.masters.reviews.store');
    Route::put('/reviews/{reviewId}', [\App\Http\Controllers\Admin\MasterController::class, 'updateReview'])->name('admin.api.reviews.update');
    Route::delete('/reviews/{reviewId}', [\App\Http\Controllers\Admin\MasterController::class, 'deleteReview'])->name('admin.api.reviews.delete');

    // Users (needed by admin master edit page)
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.api.users.index');

    // Services management (admin)
    Route::get('/admin-services', [\App\Http\Controllers\Admin\ServiceController::class, 'list'])->name('admin.api.admin_services.list');
    Route::get('/admin-services/{id}', [\App\Http\Controllers\Admin\ServiceController::class, 'get'])->name('admin.api.admin_services.get');
    Route::put('/admin-services/{id}', [\App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('admin.api.admin_services.update');
    Route::put('/admin-services/{id}/providers', [\App\Http\Controllers\Admin\ServiceController::class, 'updateProviders'])->name('admin.api.admin_services.update_providers');
    Route::get('/admin-services/{id}/delete-preview', [\App\Http\Controllers\Admin\ServiceController::class, 'deletePreview'])->name('admin.api.admin_services.delete_preview');
    Route::delete('/admin-services/{id}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('admin.api.admin_services.destroy');
    Route::post('/admin-services/bulk/delete-preview', [\App\Http\Controllers\Admin\ServiceController::class, 'bulkDeletePreview'])->name('admin.api.admin_services.bulk_delete_preview');
    Route::post('/admin-services/bulk/delete', [\App\Http\Controllers\Admin\ServiceController::class, 'bulkDestroy'])->name('admin.api.admin_services.bulk_delete');
});

// Admin OTP auth routes (session login)
Route::group(['prefix' => 'admin-auth'], function () {
    Route::post('/request-otp', [\App\Http\Controllers\Admin\AuthController::class, 'requestOtp'])->name('admin.auth.request_otp');
    Route::post('/verify-otp', [\App\Http\Controllers\Admin\AuthController::class, 'verifyOtp'])->name('admin.auth.verify_otp');
});
