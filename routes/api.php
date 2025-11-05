<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\taskController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// route for authentication
Route::post('register',[AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
Route::post('refresh', [AuthController::class,'refresh']);
Route::post('logout', [AuthController::class,'logout']);

// route for tasks
Route::middleware('auth:sanctum')->group(function () {
    Route::get('tasks', [taskController::class,'index']);
    Route::get('tasks/{id}', [taskController::class,'show']);
    Route::post('tasks', [taskController::class,'store']);
    Route::put('tasks/{id}', [taskController::class,'update']);
    Route::delete('tasks/{id}', [taskController::class,'destroy']);
});