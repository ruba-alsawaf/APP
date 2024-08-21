<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\ConsultationCategory;
use App\Models\Expert;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/register', [AuthController::class, 'register']); 
    Route::post('/complete-profile', [AuthController::class, 'completeProfile']);
    Route::get('/user-info', [AuthController::class, 'getUser']);
    Route::get('/experts/category/{category}', [AuthController::class, 'getExpertsByCategory']);
});

    Route::get('/', function () {
        return response()->json(['message' => 'Welcome to the API']);
    });
    
