<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TrainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SMSController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::put('/user/edit', [UserController::class, 'updateUser']);

//Get all users
Route::get('/users', [UserController::class, 'index']);

//Get specific user
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}/edit', [UserController::class, 'update']);
Route::post('/users', [UserController::class, 'create']);

//Auth Users
Route::post('/user-login', [UserController::class, 'login']);
Route::post('/user-logout', [UserController::class, 'logout']);

//Get all trains
Route::get('/trains', [TrainController::class, 'index']);

//Get all notification
Route::get('/notifications', [NotificationController::class, 'index']);
Route::put('/notifications/{user_id}/edit', [NotificationController::class, 'update']);
Route::get('/notifications/{user_id}', [NotificationController::class, 'show']);
Route::get('/notifications/showSpecific/{user_id}', [NotificationController::class, 'showSpecific']);
Route::get('/notifications/showAll/{user_id}', [NotificationController::class, 'showAll']);

//send sms
Route::post('/send-twilio', [SMSController::class, 'sendSmsTwilio']);