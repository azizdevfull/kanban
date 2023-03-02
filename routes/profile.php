<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;

// use Illuminate\Routing\Route;

Route::post('/profile', [ProfileController::class, 'Profile'])->middleware('auth:sanctum');
Route::post('/profile-update', [ProfileController::class, 'updateProfile'])->middleware('auth:sanctum');
