<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\PasswordResetController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::get('/', function () {
    return 'api connected';
});

Route::apiResource('trips', TripController::class);
Route::post('trips/{trip}', [TripController::class, 'customPostMethod']);
Route::get('/users/{userId}/trips', [TripController::class, 'getUserTrips']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/check-email', [AuthController::class, 'checkEmail']);
Route::post('/check-username', [AuthController::class, 'checkUsername']);
Route::post('/check-nickname', [AuthController::class, 'checkNickname']);

Route::post('/send-reset-password-code', [PasswordResetController::class, 'sendResetCode']);
Route::post('/verify-reset-password-code', [PasswordResetController::class, 'verifyCode']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
