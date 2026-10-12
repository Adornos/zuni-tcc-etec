<?php

use App\Enums\ClassroomGrade;
use App\Enums\ReportTarget;
use App\Enums\ReportType;
use App\Enums\UserRole;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Report;
use App\Models\StudentSheet;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

/* ---------------------------------------------------------------------- */
/* Helpers (prefixo "rp" para não colidir com outros testes)               */
/* ---------------------------------------------------------------------- */

function rpUser(UserRole $role, array $attrs = []): User
{
    static $n = 0;
    $n++;

    return User::factory()->create(array_merge([
        'role' => $role->value,
        'username' => "{$role->value}-{$n}",
        'name' => ucfirst($role->value)."_{$n}_",
    ], $attrs));
}

function rpClassroom(array $teachers = []): Classroom
{
    static $n = 0;
    $n++;

    $classroom = Classroom::create([
        'name' => "Sala {$n}",
        'grade' => ClassroomGrade::FIRST_YEAR->value,
    ]);

    foreach ($teachers as $teacher) {
        $classroom->teachers()->attach($teacher->id);
    }

    return $classroom;
}

function rpStudent(?Classroom $classroom = null, ?User $guardian = null): StudentSheet
{
    $guardian ??= rpUser(UserRole::GUARDIAN);
    $user = rpUser(UserRole::STUDENT);

    return StudentSheet::create([
        'student_id' => $user->id,
        'guardian_id' => $guardian->id,
        'classroom_id' => $classroom?->id,
    ]);
}

function rpReport(User $author, ReportTarget $target, array $ids, array $attrs = []): Report
{
    // unguarded: permite fixar created_at nos testes de ordenação.
    $report = Report::unguarded(fn () => Report::create(array_merge([
        'title' => 'Título do relatório',
        'content' => 'Corpo do relatório',
        'type' => ReportType::NOTICE,
        'target' => $target,
        'is_active' => true,
        'author_id' => $author->id,
    ], $attrs)));

    $target === ReportTarget::STUDENTS
        ? $report->studentSheets()->attach($ids)
        : $report->classrooms()->attach($ids);

    return $report;
}

function rpPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Aviso importante',
        'content' => 'Texto do aviso',
        'type' => 'notice',
        'target' => 'students',
    ], $overrides);
}

/* ---------------------------------------------------------------------- */
/* Criação                                                                 */
/* ---------------------------------------------------------------------- */

it('permite que o professor crie um report para um aluno sob sua responsabilidade', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload(['students' => [$sheet->id]]))
        ->assertRedirect();

    $report = Report::sole();
    expect($report->author_id)->toBe($teacher->id)
        ->and($report->is_active)->toBeTrue()
        ->and($report->target)->toBe(ReportTarget::STUDENTS)
        ->and($report->studentSheets()->pluck('student_sheets.id')->all())->toBe([$sheet->id])
        ->and($report->classrooms()->count())->toBe(0);
});

it('permite que o professor crie um report para vários alunos', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher]);
    $a = rpStudent($room);
    $b = rpStudent($room);

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload(['students' => [$a->id, $b->id]]))
        ->assertRedirect();

    expect(Report::sole()->studentSheets()->count())->toBe(2);
});

it('permite que o professor crie um report para uma sala sua', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher]);

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload(['target' => 'classrooms', 'classrooms' => [$room->id]]))
        ->assertRedirect();

    $report = Report::sole();
    expect($report->target)->toBe(ReportTarget::CLASSROOMS)
        ->and($report->classrooms()->pluck('classrooms.id')->all())->toBe([$room->id])
        ->and($report->studentSheets()->count())->toBe(0);
});

it('permite que o coordenador crie um report para qualquer aluno ou sala', function () {
    $coordinator = rpUser(UserRole::COORDINATOR);
    $sheet = rpStudent(rpClassroom());

    $this->actingAs($coordinator)
        ->post(route('coordinator.report.store'), rpPayload(['students' => [$sheet->id]]))
        ->assertRedirect();

    $this->actingAs($coordinator)
        ->post(route('coordinator.report.store'), rpPayload([
            'target' => 'classrooms',
            'classrooms' => [rpClassroom()->id],
        ]))
        ->assertRedirect();

    expect(Report::count())->toBe(2);
});

it('nega a criação para responsável e diretor', function () {
    $sheet = rpStudent(rpClassroom());

    foreach ([UserRole::GUARDIAN, UserRole::DIRECTOR] as $role) {
        $user = rpUser($role);

        expect(Gate::forUser($user)->allows('create', Report::class))->toBeFalse();
    }

    // Responsável tentando usar a URL do professor: barrado pelo middleware de Role.
    $this->actingAs(rpUser(UserRole::GUARDIAN))
        ->post(route('teacher.report.store'), rpPayload(['students' => [$sheet->id]]))
        ->assertForbidden();

    expect(Report::count())->toBe(0);
});

it('rejeita report sem atribuições', function () {
    $teacher = rpUser(UserRole::TEACHER);

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload())
        ->assertSessionHasErrors('students');

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload(['target' => 'classrooms']))
        ->assertSessionHasErrors('classrooms');

    expect(Report::count())->toBe(0);
});

it('rejeita atribuição simultânea a alunos e salas', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher]);
    $sheet = rpStudent($room);

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload([
            'students' => [$sheet->id],
            'classrooms' => [$room->id],
        ]))
        ->assertSessionHasErrors('classrooms');

    expect(Report::count())->toBe(0);
});

it('rejeita entidades inexistentes', function () {
    $teacher = rpUser(UserRole::TEACHER);

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload(['students' => [999999]]))
        ->assertSessionHasErrors('students.0');

    expect(Report::count())->toBe(0);
});

it('rejeita alunos e salas fora da responsabilidade do professor', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $own = rpStudent(rpClassroom([$teacher]));
    $foreign = rpStudent(rpClassroom());
    $foreignRoom = rpClassroom();

    // Um aluno autorizado + um não autorizado: nada é salvo.
    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload(['students' => [$own->id, $foreign->id]]))
        ->assertSessionHasErrors('students');

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload(['target' => 'classrooms', 'classrooms' => [$foreignRoom->id]]))
        ->assertSessionHasErrors('classrooms');

    expect(Report::count())->toBe(0)
        ->and(DB::table('report_student_sheet')->count())->toBe(0);
});

it('ignora autor e estado enviados pelo formulário', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $other = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));

    $this->actingAs($teacher)
        ->post(route('teacher.report.store'), rpPayload([
            'students' => [$sheet->id],
            'author_id' => $other->id,
            'is_active' => 0,
        ]));

    $report = Report::sole();
    expect($report->author_id)->toBe($teacher->id)
        ->and($report->is_active)->toBeTrue();
});

it('valida o contexto da página de criação', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $own = rpStudent(rpClassroom([$teacher]));
    $foreign = rpStudent(rpClassroom());
    $room = rpClassroom([$teacher]);

    $this->actingAs($teacher)
        ->get(route('teacher.report.create', ['students' => [$own->id]]))
        ->assertOk();

    $this->actingAs($teacher)
        ->get(route('teacher.report.create', ['students' => [$foreign->id]]))
        ->assertForbidden();

    $this->actingAs($teacher)
        ->get(route('teacher.report.create', ['students' => [999999]]))
        ->assertForbidden();

    $this->actingAs($teacher)
        ->get(route('teacher.report.create', ['students' => [$own->id], 'classrooms' => [$room->id]]))
        ->assertStatus(422);
});

it('impede que a tabela pivot de salas receba um report de alunos (exclusividade no banco)', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $room = rpClassroom([$teacher]);
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);

    expect(fn () => DB::table('report_classroom')->insert([
        'report_id' => $report->id,
        'target' => 'classrooms',
        'classroom_id' => $room->id,
    ]))->toThrow(QueryException::class);
});

/* ---------------------------------------------------------------------- */
/* Edição e exclusão                                                       */
/* ---------------------------------------------------------------------- */

it('permite que o autor edite o próprio report e remova parte das atribuições', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher]);
    $a = rpStudent($room);
    $b = rpStudent($room);
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$a->id, $b->id]);

    $this->actingAs($teacher)
        ->put(route('teacher.report.update', $report), rpPayload([
            'title' => 'Novo título',
            'type' => 'observation',
            'students' => [$a->id],
        ]))
        ->assertRedirect();

    $report->refresh();
    expect($report->title)->toBe('Novo título')
        ->and($report->type)->toBe(ReportType::OBSERVATION)
        ->and($report->studentSheets()->pluck('student_sheets.id')->all())->toBe([$a->id])
        ->and($report->author_id)->toBe($teacher->id);
});

it('impede edição de report de outro autor, inclusive pelo coordenador', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);

    $otherTeacher = rpUser(UserRole::TEACHER);
    $coordinator = rpUser(UserRole::COORDINATOR);

    $this->actingAs($otherTeacher)
        ->get(route('teacher.report.edit', $report))->assertForbidden();
    $this->actingAs($otherTeacher)
        ->put(route('teacher.report.update', $report), rpPayload(['title' => 'X', 'students' => [$sheet->id]]))
        ->assertForbidden();

    $this->actingAs($coordinator)
        ->get(route('coordinator.report.edit', $report))->assertForbidden();
    $this->actingAs($coordinator)
        ->put(route('coordinator.report.update', $report), rpPayload(['title' => 'X', 'students' => [$sheet->id]]))
        ->assertForbidden();

    expect($report->fresh()->title)->toBe('Título do relatório');
});

it('não permite salvar sem atribuições nem trocar a categoria na edição', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher]);
    $sheet = rpStudent($room);
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);

    $this->actingAs($teacher)
        ->put(route('teacher.report.update', $report), rpPayload())
        ->assertSessionHasErrors('students');

    $this->actingAs($teacher)
        ->put(route('teacher.report.update', $report), rpPayload(['target' => 'classrooms', 'classrooms' => [$room->id]]))
        ->assertSessionHasErrors('target');

    expect($report->fresh()->studentSheets()->count())->toBe(1)
        ->and($report->fresh()->classrooms()->count())->toBe(0)
        ->and($report->fresh()->target)->toBe(ReportTarget::STUDENTS);
});

it('não grava nada quando a edição inclui aluno fora da responsabilidade', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $own = rpStudent(rpClassroom([$teacher]));
    $foreign = rpStudent(rpClassroom());
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$own->id]);

    $this->actingAs($teacher)
        ->put(route('teacher.report.update', $report), rpPayload([
            'title' => 'Alterado',
            'students' => [$own->id, $foreign->id],
        ]))
        ->assertSessionHasErrors('students');

    expect($report->fresh()->title)->toBe('Título do relatório')
        ->and($report->fresh()->studentSheets()->count())->toBe(1);
});

it('permite que o autor exclua definitivamente e remove as associações', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);

    $this->actingAs($teacher)
        ->delete(route('teacher.report.destroy', $report))
        ->assertRedirect(route('teacher.report.index'));

    expect(Report::count())->toBe(0)
        ->and(DB::table('report_student_sheet')->count())->toBe(0);
});

it('impede exclusão por quem não é o autor, inclusive o coordenador', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);

    $this->actingAs(rpUser(UserRole::COORDINATOR))
        ->delete(route('coordinator.report.destroy', $report))
        ->assertForbidden();

    $this->actingAs(rpUser(UserRole::TEACHER))
        ->delete(route('teacher.report.destroy', $report))
        ->assertForbidden();

    expect(Report::count())->toBe(1);
});

it('permite que o coordenador edite e exclua o que ele mesmo criou', function () {
    $coordinator = rpUser(UserRole::COORDINATOR);
    $room = rpClassroom();
    $report = rpReport($coordinator, ReportTarget::CLASSROOMS, [$room->id]);

    $this->actingAs($coordinator)
        ->put(route('coordinator.report.update', $report), rpPayload([
            'title' => 'Meu título',
            'target' => 'classrooms',
            'classrooms' => [$room->id],
        ]))
        ->assertRedirect();

    expect($report->fresh()->title)->toBe('Meu título');

    $this->actingAs($coordinator)
        ->delete(route('coordinator.report.destroy', $report))
        ->assertRedirect();

    expect(Report::count())->toBe(0);
});

/* ---------------------------------------------------------------------- */
/* Estado (inativar / reativar)                                            */
/* ---------------------------------------------------------------------- */

it('permite que o autor inative o próprio report mantendo as associações', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);

    $this->actingAs($teacher)
        ->patch(route('teacher.report.status', $report), ['is_active' => 0])
        ->assertRedirect();

    expect($report->fresh()->is_active)->toBeFalse()
        ->and($report->fresh()->studentSheets()->count())->toBe(1);
});

it('permite que o coordenador inative e reative reports de outros autores sem alterar o conteúdo', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);
    $coordinator = rpUser(UserRole::COORDINATOR);

    $this->actingAs($coordinator)
        ->patch(route('coordinator.report.status', $report), ['is_active' => 0])
        ->assertRedirect();

    expect($report->fresh()->is_active)->toBeFalse();

    $this->actingAs($coordinator)
        ->patch(route('coordinator.report.status', $report), ['is_active' => 1])
        ->assertRedirect();

    $fresh = $report->fresh();
    expect($fresh->is_active)->toBeTrue()
        ->and($fresh->title)->toBe('Título do relatório')
        ->and($fresh->author_id)->toBe($teacher->id)
        ->and($fresh->studentSheets()->count())->toBe(1);
});

it('impede alteração de estado por professor não autor e por perfis sem permissão', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $report = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id], ['is_active' => false]);

    $this->actingAs(rpUser(UserRole::TEACHER))
        ->patch(route('teacher.report.status', $report), ['is_active' => 1])
        ->assertForbidden();

    $this->actingAs(rpUser(UserRole::GUARDIAN))
        ->patch("/teacher/reports/{$report->id}/status", ['is_active' => 1])
        ->assertForbidden();

    foreach ([UserRole::GUARDIAN, UserRole::DIRECTOR] as $role) {
        expect(Gate::forUser(rpUser($role))->allows('toggleActive', $report))->toBeFalse();
    }

    expect($report->fresh()->is_active)->toBeFalse();
});

/* ---------------------------------------------------------------------- */
/* Visualização                                                            */
/* ---------------------------------------------------------------------- */

it('mostra ao responsável apenas reports ativos dos próprios filhos', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher]);
    $guardian = rpUser(UserRole::GUARDIAN);
    $mine = rpStudent($room, $guardian);
    $other = rpStudent($room);

    $shared = rpReport($teacher, ReportTarget::STUDENTS, [$mine->id, $other->id]);
    $foreignOnly = rpReport($teacher, ReportTarget::STUDENTS, [$other->id]);
    $inactive = rpReport($teacher, ReportTarget::STUDENTS, [$mine->id], ['is_active' => false]);
    $roomReport = rpReport($teacher, ReportTarget::CLASSROOMS, [$room->id]);

    $this->actingAs($guardian)
        ->get(route('guardian.report.show', $shared))
        ->assertOk()
        ->assertSee($mine->user->name)
        ->assertDontSee($other->user->name); // não vaza os demais alunos

    $this->actingAs($guardian)->get(route('guardian.report.show', $foreignOnly))->assertForbidden();
    $this->actingAs($guardian)->get(route('guardian.report.show', $inactive))->assertForbidden();
    $this->actingAs($guardian)->get(route('guardian.report.show', $roomReport))->assertForbidden();
});

it('aplica as regras de visibilidade do professor', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $colleague = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher, $colleague]);
    $sheet = rpStudent($room);
    $foreignSheet = rpStudent(rpClassroom([$colleague]));

    $ownInactive = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id], ['is_active' => false]);
    $colleagueActive = rpReport($colleague, ReportTarget::STUDENTS, [$sheet->id]);
    $colleagueInactive = rpReport($colleague, ReportTarget::STUDENTS, [$sheet->id], ['is_active' => false]);
    $outsideScope = rpReport($colleague, ReportTarget::STUDENTS, [$foreignSheet->id]);

    $this->actingAs($teacher)->get(route('teacher.report.show', $ownInactive))->assertOk();
    $this->actingAs($teacher)->get(route('teacher.report.show', $colleagueActive))->assertOk();
    $this->actingAs($teacher)->get(route('teacher.report.show', $colleagueInactive))->assertForbidden();
    $this->actingAs($teacher)->get(route('teacher.report.show', $outsideScope))->assertForbidden();
});

it('permite ao coordenador ver reports ativos e inativos, e ao diretor somente os ativos', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $sheet = rpStudent(rpClassroom([$teacher]));
    $active = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id]);
    $inactive = rpReport($teacher, ReportTarget::STUDENTS, [$sheet->id], ['is_active' => false]);

    $coordinator = rpUser(UserRole::COORDINATOR);
    $this->actingAs($coordinator)->get(route('coordinator.report.show', $active))->assertOk();
    $this->actingAs($coordinator)->get(route('coordinator.report.show', $inactive))->assertOk();

    $director = rpUser(UserRole::DIRECTOR);
    $this->actingAs($director)->get(route('director.report.show', $active))->assertOk();
    $this->actingAs($director)->get(route('director.report.show', $inactive))->assertForbidden();

    foreach (['create'] as $ability) {
        expect(Gate::forUser($director)->allows($ability, Report::class))->toBeFalse();
    }
    foreach (['update', 'delete', 'toggleActive'] as $ability) {
        expect(Gate::forUser($director)->allows($ability, $active))->toBeFalse();
    }
});

it('lista no índice, ordenada do mais recente ao mais antigo e paginada', function () {
    $coordinator = rpUser(UserRole::COORDINATOR);
    $room = rpClassroom();

    foreach (range(1, 17) as $i) {
        rpReport($coordinator, ReportTarget::CLASSROOMS, [$room->id], [
            'title' => sprintf('Rel-%02d', $i),
            'created_at' => now()->subDays(20 - $i),
        ]);
    }

    $this->actingAs($coordinator)
        ->get(route('coordinator.report.index'))
        ->assertOk()
        ->assertSeeInOrder(['Rel-17', 'Rel-16', 'Rel-03'])
        ->assertDontSee('Rel-02')   // 15 por página
        ->assertDontSee('Rel-01');
});

it('mostra ao professor, no índice, somente os reports que ele criou (ativos e inativos)', function () {
    $teacher = rpUser(UserRole::TEACHER);
    $colleague = rpUser(UserRole::TEACHER);
    $room = rpClassroom([$teacher, $colleague]);

    rpReport($teacher, ReportTarget::CLASSROOMS, [$room->id], ['title' => 'MEU-ATIVO']);
    rpReport($teacher, ReportTarget::CLASSROOMS, [$room->id], ['title' => 'MEU-INATIVO', 'is_active' => false]);
    rpReport($colleague, ReportTarget::CLASSROOMS, [$room->id], ['title' => 'COLEGA-ATIVO']);

    $this->actingAs($teacher)
        ->get(route('teacher.report.index'))
        ->assertOk()
        ->assertSee('MEU-ATIVO')
        ->assertSee('MEU-INATIVO')
        ->assertDontSee('COLEGA-ATIVO');
});

it('exibe os reports ativos do aluno na página do aluno', function () {
    $coordinator = rpUser(UserRole::COORDINATOR);
    $sheet = rpStudent(rpClassroom());

    // A página do aluno, para o coordenador, monta as rotas de aprovar/rejeitar matrícula
    // a partir do Enrollment; sem ele a view lança UrlGenerationException (HTTP 500).
    Enrollment::create(['sheet_id' => $sheet->id]);

    rpReport($coordinator, ReportTarget::STUDENTS, [$sheet->id], ['title' => 'NA-PAGINA-DO-ALUNO']);
    rpReport($coordinator, ReportTarget::STUDENTS, [$sheet->id], ['title' => 'INATIVO-OCULTO', 'is_active' => false]);

    $this->actingAs($coordinator)
        ->get(route('coordinator.student.show', $sheet->student_id))
        ->assertOk()
        ->assertSee('NA-PAGINA-DO-ALUNO')
        ->assertDontSee('INATIVO-OCULTO');
});
