<?php

use App\Enums\ClassroomGrade;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\CoordinatorSheet;
use App\Models\DirectorSheet;
use App\Models\StudentSheet;
use App\Models\TeacherSheet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/* Helpers (prefixo "ux") */

function uxUser(UserRole $role, array $attrs = []): User
{
    static $n = 0;
    $n++;

    return User::factory()->create(array_merge([
        'role' => $role->value,
        'username' => "ux-{$role->value}-{$n}",
        'name' => ucfirst($role->value)."_{$n}_",
        'status' => UserStatus::ACTIVE->value,
    ], $attrs));
}

const UX_ROLES = ['guardian', 'teacher', 'coordinator', 'director'];

/* ---------------------------------------------------------------------- */
/* Tela única de perfil                                                    */
/* ---------------------------------------------------------------------- */

it('mostra a mesma tela de perfil para todos os roles, com o nome, o cargo e o botão de editar', function (string $role) {
    $user = uxUser(UserRole::from($role), ['phone' => '(11) 98888-7777', 'city' => 'São Paulo']);

    $this->actingAs($user)
        ->get(route("{$role}.profile"))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->role->label())
        ->assertSee($user->email)
        ->assertSee('(11) 98888-7777')
        ->assertSee('Editar perfil')
        ->assertSee(route("{$role}.profile.edit"), false);
})->with(UX_ROLES);

it('mostra a ficha profissional para professor, coordenador e diretor', function () {
    $teacher = uxUser(UserRole::TEACHER);
    TeacherSheet::create(['teacher_id' => $teacher->id, 'formation' => 'Pedagogia PROF', 'registration' => 'REG-T']);

    $coordinator = uxUser(UserRole::COORDINATOR);
    CoordinatorSheet::create(['coordinator_id' => $coordinator->id, 'formation' => 'Psicologia COORD']);

    $director = uxUser(UserRole::DIRECTOR);
    DirectorSheet::create(['director_id' => $director->id, 'formation' => 'Gestão DIR']);

    $this->actingAs($teacher)->get(route('teacher.profile'))->assertOk()->assertSee('Pedagogia PROF')->assertSee('Formação profissional');
    $this->actingAs($coordinator)->get(route('coordinator.profile'))->assertOk()->assertSee('Psicologia COORD');
    $this->actingAs($director)->get(route('director.profile'))->assertOk()->assertSee('Gestão DIR');
});

it('mostra os alunos vinculados ao responsável e não mostra a ficha profissional', function () {
    $guardian = uxUser(UserRole::GUARDIAN);
    $room = Classroom::create(['name' => 'Sala UX', 'grade' => ClassroomGrade::FIRST_YEAR->value]);
    $student = uxUser(UserRole::STUDENT);
    StudentSheet::create(['student_id' => $student->id, 'guardian_id' => $guardian->id, 'classroom_id' => $room->id]);

    $this->actingAs($guardian)
        ->get(route('guardian.profile'))
        ->assertOk()
        ->assertSee('Alunos vinculados')
        ->assertSee($student->name)
        ->assertSee('Sala UX')
        ->assertDontSee('Formação profissional');
});

it('abre o formulário de edição único para todos os roles', function (string $role) {
    $user = uxUser(UserRole::from($role));

    $this->actingAs($user)
        ->get(route("{$role}.profile.edit"))
        ->assertOk()
        ->assertSee(route("{$role}.profile.update"), false)
        ->assertSee('Salvar Alterações');
})->with(UX_ROLES);

/* ---------------------------------------------------------------------- */
/* Atualização                                                             */
/* ---------------------------------------------------------------------- */

it('atualiza os dados do próprio perfil em qualquer role', function (string $role) {
    $user = uxUser(UserRole::from($role));

    $this->actingAs($user)
        ->put(route("{$role}.profile.update"), [
            'name' => 'Nome Atualizado',
            'email' => "novo-{$role}@zuni.test",
            'phone' => '(11) 97777-6666',
            'gender' => 'F',
            'city' => 'Santos',
        ])
        ->assertRedirect(route("{$role}.profile"));

    $user->refresh();
    expect($user->name)->toBe('Nome Atualizado')
        ->and($user->email)->toBe("novo-{$role}@zuni.test")
        ->and($user->phone)->toBe('(11) 97777-6666')
        ->and($user->gender)->toBe('F')
        ->and($user->city)->toBe('Santos');
})->with(UX_ROLES);

it('nunca altera cargo, status ou matrícula por meio do formulário de perfil', function () {
    $teacher = uxUser(UserRole::TEACHER);

    $this->actingAs($teacher)
        ->put(route('teacher.profile.update'), [
            'name' => 'Professor Honesto',
            'email' => $teacher->email,
            'role' => 'director',
            'status' => 'suspended',
            'registration_number' => '999',
        ])
        ->assertRedirect();

    $teacher->refresh();
    expect($teacher->role)->toBe(UserRole::TEACHER)
        ->and($teacher->status)->toBe(UserStatus::ACTIVE)
        ->and($teacher->registration_number)->not->toBe('999');
});

it('troca a senha quando a confirmação confere e recusa quando não confere', function () {
    $user = uxUser(UserRole::COORDINATOR, ['password' => 'senha-antiga-1']);

    $this->actingAs($user)
        ->put(route('coordinator.profile.update'), [
            'name' => $user->name, 'email' => $user->email,
            'password' => 'NovaSenha123', 'password_confirmation' => 'diferente',
        ])
        ->assertSessionHasErrors('password');

    expect(Hash::check('senha-antiga-1', $user->fresh()->password))->toBeTrue();

    $this->actingAs($user)
        ->put(route('coordinator.profile.update'), [
            'name' => $user->name, 'email' => $user->email,
            'password' => 'NovaSenha123', 'password_confirmation' => 'NovaSenha123',
        ])
        ->assertRedirect(route('coordinator.profile'));

    expect(Hash::check('NovaSenha123', $user->fresh()->password))->toBeTrue();
});

it('mantém a senha quando o campo vem em branco', function () {
    $user = uxUser(UserRole::GUARDIAN, ['password' => 'senha-original-1']);

    $this->actingAs($user)
        ->put(route('guardian.profile.update'), ['name' => 'Outro Nome', 'email' => $user->email, 'password' => '', 'password_confirmation' => ''])
        ->assertRedirect();

    expect(Hash::check('senha-original-1', $user->fresh()->password))->toBeTrue();
});

it('valida e-mail e CPF únicos (ignorando o próprio) e exige nome e e-mail', function () {
    $a = uxUser(UserRole::TEACHER, ['cpf' => '111.111.111-11']);
    $b = uxUser(UserRole::TEACHER);

    $this->actingAs($b)
        ->put(route('teacher.profile.update'), ['name' => $b->name, 'email' => $a->email])
        ->assertSessionHasErrors('email');

    $this->actingAs($b)
        ->put(route('teacher.profile.update'), ['name' => $b->name, 'email' => $b->email, 'cpf' => '111.111.111-11'])
        ->assertSessionHasErrors('cpf');

    // O próprio CPF/e-mail não conta como duplicado.
    $this->actingAs($a)
        ->put(route('teacher.profile.update'), ['name' => $a->name, 'email' => $a->email, 'cpf' => '111.111.111-11'])
        ->assertSessionDoesntHaveErrors();

    $this->actingAs($b)
        ->put(route('teacher.profile.update'), ['name' => '', 'email' => ''])
        ->assertSessionHasErrors(['name', 'email']);
});

/* ---------------------------------------------------------------------- */
/* Acesso                                                                  */
/* ---------------------------------------------------------------------- */

it('não deixa um role abrir o perfil de outro role pela URL', function () {
    $guardian = uxUser(UserRole::GUARDIAN);

    $this->actingAs($guardian)->get('/teacher/profile')->assertForbidden();
    $this->actingAs($guardian)->get('/coordinator/profile/edit')->assertForbidden();
    $this->actingAs($guardian)->put('/coordinator/profile', ['name' => 'X', 'email' => 'x@x.test'])->assertForbidden();
});

it('redireciona visitantes ao login', function () {
    $this->get('/teacher/profile')->assertRedirect(route('login'));
    $this->put('/guardian/profile', ['name' => 'X'])->assertRedirect(route('login'));
});

it('só atualiza o perfil de quem está autenticado, mesmo que o formulário envie outro id', function () {
    $me = uxUser(UserRole::TEACHER);
    $other = uxUser(UserRole::TEACHER, ['name' => 'Colega Intocável']);

    $this->actingAs($me)->put(route('teacher.profile.update'), [
        'id' => $other->id, 'user_id' => $other->id, 'name' => 'Eu Mesmo', 'email' => $me->email,
    ]);

    expect($me->fresh()->name)->toBe('Eu Mesmo')->and($other->fresh()->name)->toBe('Colega Intocável');
});
