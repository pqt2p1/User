<?php

use Illuminate\Support\Facades\Route;
use Pqt2p1\User\Http\Controllers\Api\AuthController;

Route::group(
    [
        'prefix' => 'v1'
    ],
    function () {
        Route::get('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/user', [AuthController::class, 'getProfile'])->middleware('auth:sanctum');
        Route::put('/user', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
        Route::post('/password/email', [AuthController::class, 'forgotPassword']);
        Route::post('/password/reset', [AuthController::class, 'resetPassword']);
        Route::post('/password/change', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
    }
);
