<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', function ($request) {
    return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum','throttle:custom-api-limit')->group(function () { //custom-api-limit to hit any api 5 in one minut
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('posts', PostController::class);
});
// routes/api.php
Route::middleware('auth:sanctum')->post('/posts/{post}/comments', [PostController::class, 'addComment']);
Route::middleware('auth:sanctum')->delete('/posts/{post}', [PostController::class, 'destroy']);


// Route::middleware(['auth:sanctum', 'throttle:4,1'])->group(function () {
//     Route::apiResource('posts', PostController::class);
// });

