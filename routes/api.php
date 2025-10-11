<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/debug', function () {
        return response()->json([
            'message' => 'Debug route',
            'time' => now()
        ]);
    });

    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to API V1!',
            'time' => now()
        ]);
    });

    Route::prefix('auth')->group(function () {
        Route::middleware('throttle:10,1')->post('/register', [AuthController::class, 'register']);
        Route::middleware('throttle:5,1')->post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user', [AuthController::class, 'user']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        
    });
});