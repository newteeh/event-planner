<?php

use App\Http\Controllers\API\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Публичные маршруты (если будут)
// Route::post('/login', [AuthController::class, 'login']);

// Защищённые маршруты (требуют аутентификации)
Route::middleware('auth:sanctum')->group(function () {
    // Информация о текущем пользователе
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // CRUD для событий
    Route::apiResource('events', EventController::class);
    
    // Дополнительные маршруты для участников
    Route::post('/events/{event}/join', [EventController::class, 'join']);
    Route::post('/events/{event}/leave', [EventController::class, 'leave']);
    Route::put('/events/{event}/status', [EventController::class, 'updateStatus']);
});