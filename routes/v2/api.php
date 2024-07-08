<?php

use App\Http\Controllers\Api\V2\Auth\UserController as AuthUserController;
use App\Http\Controllers\Api\V2\OTPVerification\OtpVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    // 'middleware' => 'http-logger',
 ], function () {


    Route::group([
    'prefix' => 'auth',
    ], function () {
        Route::post('login', [AuthUserController::class, 'login']);
        Route::post('register', [AuthUserController::class, 'register']);
        Route::post('forget', [AuthUserController::class, 'forgetPassword']);
        Route::post('reset', [AuthUserController::class, 'resetPassword']);
        Route::group([
            'middleware' => 'auth:api',
        ], function () {
            Route::get('user', [AuthUserController::class, 'user']);
            Route::resource('otpverifications', OtpVerificationController::class);
            Route::post('logout', [AuthUserController::class, 'logout']);
            Route::delete('user', [AuthUserController::class, 'delete']);
            Route::get('checkotpverify', [OtpVerificationController::class, 'checkOtpVerify']);
            Route::post('resendotp', [OtpVerificationController::class, 'resendOtp']);

        });
    });

    Route::post('verification/forgotpassword', [OtpVerificationController::class, 'verifyme']);
    Route::post('verification/verify', [OtpVerificationController::class, 'verifyme']);
    Route::post('verification/', [OtpVerificationController::class, 'verifyme']);
    Route::post('verification/resendotp', [OtpVerificationController::class, 'resendOtp']);

});

