<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ZuniController;
use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;

Route::get('/', [ZuniController::class, 'index']);

Route::middleware(['auth', 'role:guardian'])
    ->prefix('guardian')
    ->name('guardian.')
    ->group(function () {

    // Rotas dos Responsáveis
    Route::get('/', [GuardianController::class, 'index'])->name('index');
    Route::get('/profile', [GuardianController::class, 'profile'])->name('profile');

    Route::get('/registered', [GuardianController::class, 'registered'])->name('registered');
    Route::get('/register', [GuardianController::class, 'registerStudentForm'])->name('student.register');
    Route::post('/register', [GuardianController::class, 'registerStudent'])->name('student.store');

    Route::get('/forum', [GuardianController::class, 'forum'])->name('forum');
    Route::get('/chat', [GuardianController::class, 'chat'])->name('chat');

});

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

    // Rotas dos Responsáveis
    Route::get('', [TeacherController::class, 'index'])->name('index');
    Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');

    Route::get('/schedule', [TeacherController::class, 'schedule'])->name('schedule');

    Route::get('/forum', [TeacherController::class, 'forum'])->name('forum');
    Route::get('/chat', [TeacherController::class, 'chat'])->name('chat');

    // Route::put('/guardian', [TeacherController::class, 'update']);
    // Route::delete('/guardian', [TeacherController::class, 'destroy']);

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

Route::get('/logout', Logout::class)
    ->middleware('auth')->name('logout');


