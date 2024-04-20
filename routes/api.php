<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::get('users', [AuthController::class, 'allUsers']);
    Route::post('logout', [AuthController::class, 'logout']);
});
