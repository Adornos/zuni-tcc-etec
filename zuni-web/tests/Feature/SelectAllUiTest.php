<?php

use App\Enums\ClassroomGrade;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\StudentSheet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function saUser(UserRole $role): User
{
    static $n = 0;
    $n++;

    return User::factory()->create([
        'role' => $role->value, 'username' => "sa-{$role->value}-{$n}",
        'name' => ucfirst($role->value)."_{$n}_", 'status' => UserStatus::ACTIVE->value,
    ]);
}

function saRoomWithStudents(int $n = 2): Classroom
{
    $room = Classroom::create(['name' => 'Sala SA', 'grade' => ClassroomGrade::FIRST_YEAR->value]);

    for ($i = 0; $i < $n; $i++) {
        StudentSheet::create(['student_id' => saUser(UserRole::STUDENT)->id, 'guardian_id' => saUser(UserRole::GUARDIAN)->id, 'classroom_id' => $room->id]);
    }

    return $room;
}

it('o formulário de relatório para alunos tem o botão de selecionar todos', function () {
    saRoomWithStudents();

    $this->actingAs(saUser(UserRole::COORDINATOR))
        ->get(route('coordinator.report.create'))
        ->assertOk()
        ->assertSee('Selecionar todos')
        ->assertSee('Limpar seleção')
        ->assertSee('selecionado(s)');
});

it('a lista de alunos da sala tem o botão de selecionar todos para criar relatório', function () {
    $room = saRoomWithStudents();

    $this->actingAs(saUser(UserRole::COORDINATOR))
        ->get(route('coordinator.classroom.students', $room))
        ->assertOk()
        ->assertSee('Selecionar todos')
        ->assertSee('Criar relatório para os selecionados');
});

it('a lista geral de alunos tem o botão de selecionar todos da página', function () {
    saRoomWithStudents();

    $this->actingAs(saUser(UserRole::COORDINATOR))
        ->get(route('coordinator.student.index'))
        ->assertOk()
        ->assertSee('Selecionar todos desta página')
        ->assertSee('Limpar seleção');
});
