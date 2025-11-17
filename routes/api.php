<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/debug', function () {
        return response()->json([
            'message' => 'Debug route',
            'time' => now(),
        ]);
    });

    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to API V1!',
            'time' => now(),
        ]);
    });

    Route::prefix('auth')->group(function () {
        Route::middleware('throttle:10,1')->post('/register', [AuthController::class, 'register']);
        Route::middleware('throttle:5,1')->post('/login', [AuthController::class, 'login']);

        Route::get('/auth/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->middleware(['signed'])
            ->name('verification.verify');

        // social
        Route::get('socialite/{provider}', [SocialAuthController::class, 'redirectToProvider']);
        Route::get('socialite/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/user', [AuthController::class, 'user']);
            Route::post('/auth/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
                ->middleware(['throttle:6,1'])
                ->name('verification.send');
        });
    });

    Route::middleware('auth:api')->group(function () {});
});
