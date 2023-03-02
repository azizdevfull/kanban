<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/tasks', [TaskController::class, 'store'])->middleware('auth:sanctum');
Route::post('/tasks/{task}/suggest', [TaskController::class, 'suggest'])->middleware('auth:sanctum');
Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/{task}', [TaskController::class, 'show']);
Route::put('/tasks/{task}', [TaskController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->middleware('auth:sanctum');
