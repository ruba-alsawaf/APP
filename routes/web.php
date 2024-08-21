<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpertController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/expert_dashboard', function () {
    if (Auth::guard('expert')->check()) {
        $expert = Auth::guard('expert')->user();
        return view('expert_dashboard', compact('expert'));
    } else {
        return redirect()->route('login');
    }
})->name('expert_dashboard');
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/complete-profile', [AuthController::class, 'showCompleteProfileForm'])->name('complete-profile');
Route::post('/complete-profile', [AuthController::class, 'completeProfile']);

Route::get('/dashboard', [AuthController::class, 'redirectToDashboard'])->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/', function () {
    return view('welcome');
});
