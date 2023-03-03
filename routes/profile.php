<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;

// use Illuminate\Routing\Route;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/profile', [ProfileController::class, 'Profile']);
    Route::post('/profile-update', [ProfileController::class, 'updateProfile']);
});
