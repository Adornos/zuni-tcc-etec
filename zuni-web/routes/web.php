<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\ClassroomController;
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
    Route::put('/profile', [GuardianController::class, 'profileSave'])->name('profile.save');

    Route::get('/registered', [GuardianController::class, 'registered'])->name('registered');
    Route::get('/register', [GuardianController::class, 'registerStudentForm'])->name('student.register');
    Route::post('/register', [GuardianController::class, 'registerStudent'])->name('student.store');

    Route::get('/forum', [GuardianController::class, 'forum'])->name('forum');
    Route::get('/chat', [GuardianController::class, 'chat'])->name('chat');

    // Perfil do aluno (acesso mediado pelo responsável)
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('student.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('student.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('student.update');
    Route::get('/students/{student}/schedule', [StudentController::class, 'schedule'])->name('student.schedule');
    Route::get('/students/{student}/reports', [StudentController::class, 'reports'])->name('student.reports');

});

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

    // Rotas dos Responsáveis
    Route::get('', [TeacherController::class, 'index'])->name('index');
    Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');
    Route::put('/profile', [TeacherController::class, 'profileSave'])->name('profile.save');

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


    Route::get('/', [DirectorController::class, 'index'])->name('index');
    Route::get('/profile', [DirectorController::class, 'profile'])->name('profile');


    Route::get('/employee', [DirectorController::class, 'employees'])->name('employee.index');
    Route::get('/employee/register', [DirectorController::class, 'formEmployee'])->name('employee.register');
    Route::post('/employee/register', [DirectorController::class, 'registerEmployee'])->name('employee.store');
    Route::get('/employee/show/{employee}', [DirectorController::class, 'showEmployee'])->name('employee.show');
    Route::put('/employee/show/{employee}', [DirectorController::class, 'editEmployee'])->name('employee.edit');


    Route::get('/forum', [DirectorController::class, 'forum'])->name('forum');
    Route::get('/chat', [DirectorController::class, 'chat'])->name('chat');

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
    Route::get('/students', [CoordinatorController::class, 'students'])->name('students.index');
    Route::get('/students/{enrollment}', [CoordinatorController::class, 'showStudent'])->name('enrollment.show');
    Route::put('/students/{enrollment}/approve', [CoordinatorController::class, 'approveEnrollment'])->name('enrollment.approve');
    Route::put('/students/{enrollment}/reject', [CoordinatorController::class, 'rejectEnrollment'])->name('enrollment.reject');

    // Colocação de professores
    Route::get('/teachers', [CoordinatorController::class, 'teachers'])->name('teacher.index');
    Route::get('/teacher/register', [CoordinatorController::class, 'formTeacher'])->name('teacher.register');
    Route::post('/teacher/register', [CoordinatorController::class, 'registerTeacher'])->name('teacher.store');
    Route::get('/teacher/show/{teacher}', [CoordinatorController::class, 'showTeacher'])->name('teacher.show');
    Route::put('/teacher/show/{teacher}/edit', [CoordinatorController::class, 'editTeacher'])->name('teacher.edit');

    // Criação e gerenciamento de salas
    Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classroom.index');
    Route::get('/classroom/register', [ClassroomController::class, 'create'])->name('classroom.create');
    Route::post('/classroom/register', [ClassroomController::class, 'store'])->name('classroom.store');
    Route::get('/classroom/show/{classroom}', [ClassroomController::class, 'show'])->name('classroom.show');
    Route::put('/classroom/show/{classroom}', [ClassroomController::class, 'update'])->name('classroom.update');
    Route::get('/classroom/show/{classroom}/teachers', [ClassroomController::class, 'teachers'])->name('classroom.teachers');
    Route::put('/classroom/show/{classroom}/teachers', [ClassroomController::class, 'assignTeachers'])->name('classroom.teachers.update');
    Route::get('/classroom/show/{classroom}/students', [ClassroomController::class, 'students'])->name('classroom.students');
    Route::put('/classroom/show/{classroom}/students', [ClassroomController::class, 'assignStudents'])->name('classroom.students.update');


    // Gerenciamento de horários (abordagem de grade)
    Route::get('/schedules', [CoordinatorController::class, 'schedules'])->name('schedules.index');
    Route::put('/schedules', [CoordinatorController::class, 'updateSchedules'])->name('schedules.update');
    Route::get('/schedules/students/{student}', [CoordinatorController::class, 'studentSchedule'])->name('schedules.student');
    Route::get('/schedules/teachers/{teacher}', [CoordinatorController::class, 'teacherSchedule'])->name('schedules.teacher');

    // Relatórios (internos: para docentes | externos: para responsáveis)
    Route::get('/reports', [CoordinatorController::class, 'reports'])->name('report.index');
    Route::get('/reports/create', [CoordinatorController::class, 'createReport'])->name('report.create');
    Route::post('/reports', [CoordinatorController::class, 'storeReport'])->name('report.store');
    Route::get('/reports/{report}/edit', [CoordinatorController::class, 'editReport'])->name('report.edit');
    Route::put('/reports/{report}', [CoordinatorController::class, 'updateReport'])->name('report.update');
    Route::delete('/reports/{report}', [CoordinatorController::class, 'destroyReport'])->name('report.destroy');

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