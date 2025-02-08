<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PreferenceController;

// Route for registering a new user
Route::post('/register', [AuthController::class, 'register']);

// Route for logging in to obtain a token
Route::post('/login', [AuthController::class, 'login']);

// Route for initiating password reset
Route::post('/initiate/password/reset', [AuthController::class, 'initiateResetPassword'])->name('password.email');
// Route for password reset
Route::post('/reset/password', [AuthController::class, 'resetPassword'])->name('password.update');


Route::middleware('auth:sanctum')->group(function () {
    // Route for logging out (requires authentication)
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route group for article management
    Route::get('/articles', [ArticleController::class, 'index']);      // List and filter articles
    Route::get('/articles/{id}', [ArticleController::class, 'show']);  // Retrieve a single article

    // Route to retrieve user preferences
    Route::get('/preferences', [PreferenceController::class, 'show']);
    // Route to create or update user preferences
    Route::post('/preferences', [PreferenceController::class, 'store']);
    Route::get('/personalized-feed', [PreferenceController::class, 'personalizedFeed']);
});