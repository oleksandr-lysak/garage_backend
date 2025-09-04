<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\GoogleImportController;
use App\Http\Controllers\Api\V1\MasterController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SmsVerificationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return true;
});

Route::prefix('masters')->group(function () {
    Route::get('/', [MasterController::class, 'index']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/review', [MasterController::class, 'addReview']);
    });
    Route::put('/{id}', [MasterController::class, 'updateProfile'])->middleware('auth:api');
    Route::post('/{id}/gallery', [MasterController::class, 'addGalleryPhotos'])->middleware('auth:api');
    Route::post('/import-external/{service_id}', [MasterController::class, 'storeFromExternal']);
    Route::post('/import-external/google/{service_id}', [GoogleImportController::class, 'import']);
    Route::get('/{id}', [MasterController::class, 'getMaster']);
    Route::post('/{id}/work-schedule', [MasterController::class, 'updateWorkSchedule']);
    Route::prefix('/{id}')->group(function () {
        Route::post('/availability', [MasterController::class, 'setAvailable']);
        Route::get('/availability', [MasterController::class, 'getAvailability']);
        Route::delete('/availability', [MasterController::class, 'setUnavailable']);
    });

});

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/{id}', [ServiceController::class, 'getService']);
    Route::get('/get-for-master/{master_id}', [ServiceController::class, 'getServicesForMaster']);
});

Route::prefix('auth')->group(function () {
    Route::post('/request-otp', [AuthController::class, 'requestOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::post('/master-register',
        [MasterController::class, 'verifyAndRegister']
    );
    Route::post('/client-register',
        [ClientController::class, 'register']
    );
    Route::post('/send-code', [SmsVerificationController::class, 'sendCode']);
    Route::post('/verify-code', [UserController::class, 'verifyCode']);
});



Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/protected-route', [MasterController::class, 'protectedMethod']);
});
