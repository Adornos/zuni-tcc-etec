<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\ClassroomProficiencyAverage;
use App\Models\Enrollment;
use App\Models\Proficiency;
use App\Models\StudentProficiency;
use App\Models\StudentSheet;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->seed(DatabaseSeeder::class));

it('cria 1 coordenador, 1 diretor, 5 professores, 5 responsáveis e 25 alunos', function () {
    expect(User::where('role', UserRole::COORDINATOR->value)->count())->toBe(1)
        ->and(User::where('role', UserRole::DIRECTOR->value)->count())->toBe(1)
        ->and(User::where('role', UserRole::TEACHER->value)->count())->toBe(5)
        ->and(User::where('role', UserRole::GUARDIAN->value)->count())->toBe(5)
        ->and(User::where('role', UserRole::STUDENT->value)->count())->toBe(25)
        ->and(User::count())->toBe(37);
});

it('todos os usuários estão ativos, com a senha 123456 e login por e-mail ou usuário', function () {
    expect(User::where('status', '!=', UserStatus::ACTIVE->value)->count())->toBe(0);

    foreach (['coordinator01', 'director01', 'teacher01', 'guardian01', 'aluno001'] as $username) {
        $user = User::where('username', $username)->firstOrFail();
        expect(Hash::check('123456', $user->password))->toBeTrue();
    }

    $this->post('/login', ['login' => 'coordinator@zuni.test', 'password' => '123456'])->assertRedirect();
    $this->assertAuthenticatedAs(User::where('username', 'coordinator01')->first());
});

it('cria as fichas profissionais de coordenador, diretor e professores', function () {
    expect(User::where('username', 'coordinator01')->first()->coordinatorSheet)->not->toBeNull()
        ->and(User::where('username', 'director01')->first()->directorSheet)->not->toBeNull()
        ->and(User::where('role', UserRole::TEACHER->value)->get()->filter(fn ($t) => $t->teacherSheet !== null)->count())->toBe(5);
});

it('cada responsável tem exatamente 5 alunos, um em cada turma', function () {
    foreach (User::where('role', UserRole::GUARDIAN->value)->get() as $guardian) {
        $sheets = StudentSheet::where('guardian_id', $guardian->id)->get();

        expect($sheets)->toHaveCount(5)
            ->and($sheets->pluck('classroom_id')->unique())->toHaveCount(5);

        // Irmãos compartilham o sobrenome do responsável.
        $surname = collect(explode(' ', $guardian->name))->last();
        expect($sheets->every(fn ($s) => str_ends_with($s->user->name, $surname)))->toBeTrue();
    }
});

it('cria 5 turmas com 5 alunos de 5 responsáveis diferentes e 1 professor cada', function () {
    $rooms = Classroom::with(['students', 'teachers'])->get();

    expect($rooms)->toHaveCount(5);

    foreach ($rooms as $room) {
        expect($room->students)->toHaveCount(5)
            ->and($room->students->pluck('guardian_id')->unique())->toHaveCount(5)
            ->and($room->teachers)->toHaveCount(1);
    }

    // Cada professor responde por exatamente uma turma.
    foreach (User::where('role', UserRole::TEACHER->value)->get() as $teacher) {
        expect($teacher->classrooms()->count())->toBe(1);
    }
});

it('todo aluno tem matrícula aprovada e registro único', function () {
    $sheets = StudentSheet::all();

    expect($sheets)->toHaveCount(25)
        ->and(Enrollment::count())->toBe(25)
        ->and(Enrollment::whereNull('reviewed_at')->count())->toBe(0)
        ->and($sheets->pluck('registration_number')->unique())->toHaveCount(25);
});

it('inicializa as proficiências dos alunos e as médias das turmas', function () {
    $skills = Proficiency::active()->count();

    expect($skills)->toBeGreaterThan(0)
        ->and(StudentProficiency::count())->toBe(25 * $skills)
        ->and(ClassroomProficiencyAverage::where('students_count', 5)->count())->toBe(5 * $skills);

    expect((float) ClassroomProficiencyAverage::where('students_count', 5)->avg('average'))->toBe(5.0);
});

it('as permissões fecham com os dados: professor avalia só a turma dele e responsável vê só os filhos', function () {
    $teacher = User::where('username', 'teacher01')->first();
    $own = StudentSheet::where('classroom_id', $teacher->classrooms()->first()->id)->first();
    $foreign = StudentSheet::where('classroom_id', '!=', $teacher->classrooms()->first()->id)->first();

    expect(Gate::forUser($teacher)->allows('observe', $own))->toBeTrue()
        ->and(Gate::forUser($teacher)->allows('observe', $foreign))->toBeFalse();

    $guardian = User::where('username', 'guardian01')->first();
    $child = StudentSheet::where('guardian_id', $guardian->id)->first();
    $other = StudentSheet::where('guardian_id', '!=', $guardian->id)->first();

    expect(Gate::forUser($guardian)->allows('viewProficiencies', $child))->toBeTrue()
        ->and(Gate::forUser($guardian)->allows('viewProficiencies', $other))->toBeFalse();
});

it('é idempotente: rodar de novo não duplica nada', function () {
    $before = [User::count(), Classroom::count(), StudentSheet::count(), Enrollment::count(), StudentProficiency::count(), ClassroomProficiencyAverage::count()];

    $this->seed(DatabaseSeeder::class);

    expect([User::count(), Classroom::count(), StudentSheet::count(), Enrollment::count(), StudentProficiency::count(), ClassroomProficiencyAverage::count()])->toBe($before);
});
