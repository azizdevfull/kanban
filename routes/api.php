<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/home', [HomeController::class, 'Home'])->middleware('auth:sanctum');


require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
require __DIR__.'/tasks.php';

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
