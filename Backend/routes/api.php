<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\taskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;

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

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    // Task routes
    Route::get('tasks', [taskController::class, 'index']);
    Route::get('tasks/{id}', [taskController::class, 'show']);
    Route::post('tasks', [taskController::class, 'store']);
    Route::put('tasks/{id}', [taskController::class, 'update']);
    Route::delete('tasks/{id}', [taskController::class, 'destroy']);

    // Notification routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    // User routes
    Route::get('users', [UserController::class, 'index']);
    Route::get('profile', [UserController::class, 'profile']);
});