<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\LoginHistoryController;
use Illuminate\Support\Facades\Route;

// Open routes
Route::post('register', [ApiController::class, 'register']);
Route::post('login', [ApiController::class, 'login']);

// Protected routes (milldelware -> auth:api)
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('profile', [ApiController::class, 'profile']);
    Route::get('logout', [ApiController::class, 'logout']);
    Route::get('login-histories', [LoginHistoryController::class, 'index']);
});
