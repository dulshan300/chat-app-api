<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatRoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// authorization routes
Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/ping', [AuthController::class, 'ping'])->middleware('auth:sanctum');
});


// authorised routes
Route::prefix('chat')->middleware('auth:sanctum')->group(function () {

    Route::resource('rooms', ChatRoomController::class);
    Route::post('rooms/join', [ChatRoomController::class,'join_room']);
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
