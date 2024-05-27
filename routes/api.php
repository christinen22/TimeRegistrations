<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TimeTrackingController;

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// User profile routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/time-trackings', [TimeTrackingController::class, 'index']);
    Route::post('/time-trackings', [TimeTrackingController::class, 'store']);
    Route::put('/time-trackings/{id}', [TimeTrackingController::class, 'update']);
    Route::delete('/time-trackings/{id}', [TimeTrackingController::class, 'destroy']);
});

/*
Route::get('test-cors', function () {
    return response()->json(['message' => 'CORS works!'])
        ->header('Access-Control-Allow-Origin', 'http://localhost:8080')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, DELETE, PUT')
        ->header('Access-Control-Allow-Headers', 'Authorization, Content-Type');
});
*/
