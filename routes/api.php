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
    Route::get('/categories', [AuthController::class, 'getConsultationCategories']);
    Route::get('/categories-with-experts', [AuthController::class, 'getCategoriesWithExperts']);


    Route::post('/searchName', [AuthController::class, 'searchName'])->name('search');
    Route::post('/searchConsultation_categories', [AuthController::class, 'searchConsultation_categories'])->name('search');
    Route::get('/expert/{id}', [AuthController::class, 'showExpert'])->name('expert.show');

    Route::post('/availability/add', [AuthController::class, 'addAvailableTime']);
    Route::get('/availability/{expert_id}', [AuthController::class, 'getAvailableTimes']);
    Route::post('/appointments/book', [AuthController::class, 'bookAppointment']);
    Route::get('/appointments/booked', [AuthController::class, 'getBookedAppointments']);


});

    Route::get('/', function () {
        return response()->json(['message' => 'Welcome to the API']);
    });
    
