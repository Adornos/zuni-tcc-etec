<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\StudentController;
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

    // Perfil do aluno (acesso mediado pelo responsável)
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/schedule', [StudentController::class, 'schedule'])->name('students.schedule');
    Route::get('/students/{student}/reports', [StudentController::class, 'reports'])->name('students.reports');

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

    // Relatórios internos (feitos para os docentes)
    Route::get('/reports', [TeacherController::class, 'reports'])->name('reports.index');
    Route::get('/reports/{report}', [TeacherController::class, 'showReport'])->name('reports.show');

    // Route::put('/guardian', [TeacherController::class, 'update']);
    // Route::delete('/guardian', [TeacherController::class, 'destroy']);

});

Route::middleware(['auth', 'role:director'])
    ->prefix('director')
    ->name('director.')
    ->group(function () {

    // Rotas do Diretor
    Route::get('/', [DirectorController::class, 'index'])->name('index');
    Route::get('/profile', [DirectorController::class, 'profile'])->name('profile');

    Route::get('/forum', [DirectorController::class, 'forum'])->name('forum');
    Route::get('/chat', [DirectorController::class, 'chat'])->name('chat');

    // Gerenciamento de professores
    Route::get('/teachers', [DirectorController::class, 'teachers'])->name('teachers.index');
    Route::get('/teachers/{teacher}', [DirectorController::class, 'showTeacher'])->name('teachers.show');
    Route::post('/teachers', [DirectorController::class, 'storeTeacher'])->name('teachers.store');
    Route::put('/teachers/{teacher}', [DirectorController::class, 'updateTeacher'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [DirectorController::class, 'destroyTeacher'])->name('teachers.destroy');

    // Relatórios (Diretor também pode criar/gerenciar)
    Route::get('/reports', [DirectorController::class, 'reports'])->name('reports.index');
    Route::get('/reports/create', [DirectorController::class, 'createReport'])->name('reports.create');
    Route::post('/reports', [DirectorController::class, 'storeReport'])->name('reports.store');
    Route::get('/reports/{report}/edit', [DirectorController::class, 'editReport'])->name('reports.edit');
    Route::put('/reports/{report}', [DirectorController::class, 'updateReport'])->name('reports.update');
    Route::delete('/reports/{report}', [DirectorController::class, 'destroyReport'])->name('reports.destroy');

});

Route::middleware(['auth', 'role:coordinator'])
    ->prefix('coordinator')
    ->name('coordinator.')
    ->group(function () {

    // Rotas da Coordenação
    Route::get('/', [CoordinatorController::class, 'index'])->name('index');
    Route::get('/profile', [CoordinatorController::class, 'profile'])->name('profile');

    Route::get('/forum', [CoordinatorController::class, 'forum'])->name('forum');
    Route::get('/chat', [CoordinatorController::class, 'chat'])->name('chat');

    // Aprovação de matrículas
    Route::get('/enrollments', [CoordinatorController::class, 'enrollments'])->name('enrollments.index');
    Route::get('/enrollments/{enrollment}', [CoordinatorController::class, 'showEnrollment'])->name('enrollments.show');
    Route::put('/enrollments/{enrollment}/approve', [CoordinatorController::class, 'approveEnrollment'])->name('enrollments.approve');
    Route::put('/enrollments/{enrollment}/reject', [CoordinatorController::class, 'rejectEnrollment'])->name('enrollments.reject');

    // Gerenciamento de horários (abordagem de grade)
    Route::get('/schedules', [CoordinatorController::class, 'schedules'])->name('schedules.index');
    Route::put('/schedules', [CoordinatorController::class, 'updateSchedules'])->name('schedules.update');
    Route::get('/schedules/students/{student}', [CoordinatorController::class, 'studentSchedule'])->name('schedules.student');
    Route::get('/schedules/teachers/{teacher}', [CoordinatorController::class, 'teacherSchedule'])->name('schedules.teacher');

    // Relatórios (internos: para docentes | externos: para responsáveis)
    Route::get('/reports', [CoordinatorController::class, 'reports'])->name('reports.index');
    Route::get('/reports/create', [CoordinatorController::class, 'createReport'])->name('reports.create');
    Route::post('/reports', [CoordinatorController::class, 'storeReport'])->name('reports.store');
    Route::get('/reports/{report}/edit', [CoordinatorController::class, 'editReport'])->name('reports.edit');
    Route::put('/reports/{report}', [CoordinatorController::class, 'updateReport'])->name('reports.update');
    Route::delete('/reports/{report}', [CoordinatorController::class, 'destroyReport'])->name('reports.destroy');

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