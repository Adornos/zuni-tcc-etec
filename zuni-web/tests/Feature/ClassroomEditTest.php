<?php

use App\Enums\ClassroomGrade;
use App\Enums\ClassroomShift;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\StudentSheet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function ceUser(UserRole $role): User
{
    static $n = 0;
    $n++;

    return User::factory()->create([
        'role' => $role->value, 'username' => "ce-{$role->value}-{$n}",
        'name' => ucfirst($role->value)."_{$n}_", 'status' => UserStatus::ACTIVE->value,
    ]);
}

function ceRoom(int $students = 0, array $attrs = []): Classroom
{
    $room = Classroom::create(array_merge([
        'name' => 'Turma Original', 'grade' => ClassroomGrade::FIRST_YEAR->value,
        'shift' => ClassroomShift::MORNING->value, 'capacity' => 20, 'status' => 'active',
    ], $attrs));

    for ($i = 0; $i < $students; $i++) {
        StudentSheet::create(['student_id' => ceUser(UserRole::STUDENT)->id, 'guardian_id' => ceUser(UserRole::GUARDIAN)->id, 'classroom_id' => $room->id]);
    }

    return $room;
}

function cePayload(array $over = []): array
{
    return array_merge(['name' => 'Turma Editada', 'grade' => ClassroomGrade::FIRST_YEAR->value, 'shift' => 'afternoon', 'capacity' => 25, 'status' => 'active'], $over);
}

/* ---------------------------------------------------------------------- */
/* Visualização x edição                                                   */
/* ---------------------------------------------------------------------- */

it('a página de visualização é somente leitura e só a coordenação vê o botão de editar', function () {
    $room = ceRoom(2);

    $this->actingAs(ceUser(UserRole::COORDINATOR))
        ->get(route('coordinator.classroom.show', $room))
        ->assertOk()
        ->assertSee('Turma Original')
        ->assertSee('Manhã')
        ->assertSee('Editar turma')
        ->assertSee(route('coordinator.classroom.edit', $room), false)
        // O formulário saiu da visualização.
        ->assertDontSee('name="grade"', false)
        ->assertDontSee('name="capacity"', false)
        ->assertDontSee('Salvar alterações (WIP)');

    $teacher = ceUser(UserRole::TEACHER);
    $room->teachers()->attach($teacher->id);

    $this->actingAs($teacher)
        ->get(route('teacher.classroom.show', $room))
        ->assertOk()
        ->assertSee('Turma Original')
        ->assertDontSee('Editar turma');
});

it('a tela de edição abre preenchida para a coordenação e está fechada para os demais', function () {
    $room = ceRoom(1);

    $this->actingAs(ceUser(UserRole::COORDINATOR))
        ->get(route('coordinator.classroom.edit', $room))
        ->assertOk()
        ->assertSee('Editar turma')
        ->assertSee('value="Turma Original"', false)
        ->assertSee(route('coordinator.classroom.update', $room), false);

    $this->actingAs(ceUser(UserRole::TEACHER))->get("/teacher/classroom/show/{$room->id}/edit")->assertStatus(404);
    $this->actingAs(ceUser(UserRole::GUARDIAN))->get("/coordinator/classroom/show/{$room->id}/edit")->assertForbidden();
    $this->actingAs(ceUser(UserRole::DIRECTOR))->get("/coordinator/classroom/show/{$room->id}/edit")->assertForbidden();
});

/* ---------------------------------------------------------------------- */
/* Atualização                                                             */
/* ---------------------------------------------------------------------- */

it('a coordenação atualiza a turma e volta para a visualização', function () {
    $room = ceRoom(1);

    $this->actingAs(ceUser(UserRole::COORDINATOR))
        ->put(route('coordinator.classroom.update', $room), cePayload(['status' => 'inactive']))
        ->assertRedirect(route('coordinator.classroom.show', $room));

    $room->refresh();
    expect($room->name)->toBe('Turma Editada')
        ->and($room->shift)->toBe(ClassroomShift::AFTERNOON)
        ->and($room->capacity)->toBe(25)
        ->and($room->status)->toBe('inactive');
});

it('aceita turno e capacidade em branco', function () {
    $room = ceRoom();

    $this->actingAs(ceUser(UserRole::COORDINATOR))
        ->put(route('coordinator.classroom.update', $room), cePayload(['shift' => '', 'capacity' => '']))
        ->assertRedirect();

    expect($room->fresh()->shift)->toBeNull()->and($room->fresh()->capacity)->toBeNull();
});

it('valida série, turno, status, nome e a capacidade mínima pelos matriculados', function () {
    $room = ceRoom(3);
    $coordinator = ceUser(UserRole::COORDINATOR);

    $this->actingAs($coordinator)->put(route('coordinator.classroom.update', $room), cePayload(['grade' => 'inexistente']))->assertSessionHasErrors('grade');
    $this->actingAs($coordinator)->put(route('coordinator.classroom.update', $room), cePayload(['shift' => 'madrugada']))->assertSessionHasErrors('shift');
    $this->actingAs($coordinator)->put(route('coordinator.classroom.update', $room), cePayload(['status' => 'arquivada']))->assertSessionHasErrors('status');
    $this->actingAs($coordinator)->put(route('coordinator.classroom.update', $room), cePayload(['name' => '']))->assertSessionHasErrors('name');
    $this->actingAs($coordinator)->put(route('coordinator.classroom.update', $room), cePayload(['capacity' => 2]))->assertSessionHasErrors('capacity');

    // Capacidade igual ao número de matriculados é aceita.
    $this->actingAs($coordinator)->put(route('coordinator.classroom.update', $room), cePayload(['capacity' => 3]))->assertSessionDoesntHaveErrors();

    expect($room->fresh()->name)->toBe('Turma Editada');
});

it('professor, responsável e diretor não conseguem atualizar a turma', function () {
    $room = ceRoom();

    $teacher = ceUser(UserRole::TEACHER);
    $room->teachers()->attach($teacher->id);

    // A rota de atualização do professor foi removida: 405 (a URL só aceita GET) ou 403.
    expect($this->actingAs($teacher)->put("/teacher/classroom/show/{$room->id}", cePayload())->status())->toBeIn([403, 405]);
    $this->actingAs(ceUser(UserRole::GUARDIAN))->put(route('coordinator.classroom.update', $room), cePayload())->assertForbidden();
    $this->actingAs(ceUser(UserRole::DIRECTOR))->put(route('coordinator.classroom.update', $room), cePayload())->assertForbidden();

    expect($room->fresh()->name)->toBe('Turma Original');
});
