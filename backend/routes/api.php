<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdvertisementController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

// Favorite routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{advertisement}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{advertisement}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/check/{advertisement}', [FavoriteController::class, 'check']);
});

// Message routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages/{advertisement}', [MessageController::class, 'store']);
    Route::get('/messages/{advertisement}', [MessageController::class, 'show']);
    Route::post('/messages/{advertisement}/read', [MessageController::class, 'markAsRead']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
});

// Rating routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/{user}/ratings', [RatingController::class, 'index']);
    Route::post('/users/{user}/ratings', [RatingController::class, 'store']);
    Route::put('/ratings/{rating}', [RatingController::class, 'update']);
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);
    Route::get('/users/{user}/ratings/average', [RatingController::class, 'average']);
});

// User management routes
Route::middleware([])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});

Route::get('/advertisements/search', [AdvertisementController::class, 'search']);

// Advertisement routes
Route::get('/advertisements', [AdvertisementController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/advertisements', [AdvertisementController::class, 'store']);
    Route::get('/advertisements/{advertisement}', [AdvertisementController::class, 'show']);
    Route::put('/advertisements/{advertisement}', [AdvertisementController::class, 'update']);
    Route::delete('/advertisements/{advertisement}', [AdvertisementController::class, 'destroy']);
}); 