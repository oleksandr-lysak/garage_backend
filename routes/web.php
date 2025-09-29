<?php

use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\AuthController as ApiAuthController;
use App\Http\Controllers\Web\MasterController;
use App\Http\Controllers\Web\ReviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Artisan;

// Main routes
Route::get('/', [MasterController::class, 'index'])->name('welcome');
Route::get('/masters', [MasterController::class, 'index'])->name('masters.index');
Route::get('/masters/{slug}', [MasterController::class, 'show'])->name('masters.show');

// API routes for masters
Route::prefix('web-api')->middleware('api')->group(function () {
    Route::get('/masters', [MasterController::class, 'fetchMasters'])->name('api.masters.fetch');
    Route::get('/masters/filters', [MasterController::class, 'getFilters'])->name('api.masters.filters');

    // Review routes
    Route::post('/reviews/request-otp', [ReviewController::class, 'requestOtp']);
    Route::post('/reviews/submit', [ReviewController::class, 'submit']);
    Route::get('/reviews/master/{masterId}', [ReviewController::class, 'getMasterReviews']);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Public OTP login route (no auth) at /login
Route::get('/login', function () { return Inertia::render('Admin/Auth/Login'); })->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/locale/{locale}', function ($locale) {
    session(['locale' => $locale]);

    return back();
});

// Sitemap
Route::get('sitemap.xml', function () {
    if (!file_exists(public_path('sitemap.xml'))) {
        Artisan::call('sitemap:generate');
    }
    return response()->file(public_path('sitemap.xml'), [
        'Content-Type' => 'application/xml'
    ]);
});

// Include admin routes
require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
