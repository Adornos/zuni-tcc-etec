<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\ZuniController;
use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;

Route::get('/', [ZuniController::class, 'index']);

Route::middleware(['auth', 'role:guardian'])->group(function () {

    // Only Responsável users can access this routes.
    Route::get('/guardian', [GuardianController::class, 'index']);
    // Route::post('/guardian', [GuardianController::class, 'storeChild']);
    // Route::get('/guardian', [GuardianController::class, 'edit']);
    // Route::put('/guardian', [GuardianController::class, 'update']);
    // Route::delete('/guardian', [GuardianController::class, 'destroy']);


});

// Login Routes

Route::view('/login', 'auth.login')
    ->middleware('guest')
    ->name('login');

Route::post('/login', Login::class)
    ->middleware('guest');

// Register Routes
Route::view('/register', 'auth.register')
    ->middleware('guest')
    ->name('register') ;

Route::post('/register', Register::class)
    ->middleware('guest');


// Logout

Route::post('/logout', Logout::class)
    ->middleware('auth');


