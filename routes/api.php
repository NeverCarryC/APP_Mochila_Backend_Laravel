<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackpackController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TripCategoryController;
use App\Http\Controllers\ImageController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::get('/', function () {
    return 'api connected';
});

// Trip api
Route::get('/trips/by-user', [TripController::class, 'getUserTrips']);
Route::apiResource('trips', TripController::class);
Route::post('trips/{trip}', [TripController::class, 'customPostMethod']);
Route::get('/user/trips/grouped', [TripController::class, 'getTripsGroupedByCategory']);
// User api
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/check-email', [AuthController::class, 'checkEmail']);
Route::post('/check-username', [AuthController::class, 'checkUsername']);
Route::post('/check-nickname', [AuthController::class, 'checkNickname']);

Route::post('/send-reset-password-code', [PasswordResetController::class, 'sendResetCode']);
Route::post('/verify-reset-password-code', [PasswordResetController::class, 'verifyCode']);

Route::post('/send-register-code', [PasswordResetController::class, 'sendVertifyCode']);
Route::post('/verify-register-code', [PasswordResetController::class, 'verifyRegisterCode']);

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
Route::post('/update-profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');

// Backpacks api
Route::get('/backpacks/by-user', [BackpackController::class, 'getBackpacksByUser']);
Route::apiResource('backpacks', BackpackController::class);
Route::get('/backpacks/trip/{trip_id}', [BackpackController::class, 'getBackpacksByTrip']);

// Item api
Route::apiResource('items', ItemController::class);
Route::get('items/backpacks/{backpack_id}', [ItemController::class, 'getItemsBybackpack']);

// Item_category api

Route::apiResource('item_categories', ItemCategoryController::class);


// Color api
Route::apiResource('colors', ColorController::class);

// Trip category api
Route::get('trip_categories/by-user', [TripCategoryController::class, 'getTripCategoriesByUser']);
Route::apiResource('trip_categories', TripCategoryController::class);
Route::prefix('trips/{trip}')->group(function () {
    Route::post('/categories/attach', [TripCategoryController::class, 'attachCategories']);
    Route::post('/categories/detach', [TripCategoryController::class, 'detachCategories']);
});
