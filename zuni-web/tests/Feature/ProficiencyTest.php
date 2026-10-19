<?php

use App\Enums\ClassroomGrade;
use App\Enums\ObservationDirection as Dir;
use App\Enums\ObservationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\ClassroomProficiencyAverage;
use App\Models\ClassroomProficiencySnapshot;
use App\Models\Enrollment;
use App\Models\Proficiency;
use App\Models\ProficiencyObservation;
use App\Models\StudentProficiency;
use App\Models\StudentSheet;
use App\Models\User;
use App\Services\Proficiency\ClassroomAverageService;
use App\Services\Proficiency\ProficiencyService;
use App\Services\Proficiency\ProficiencyValueResolver;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

/*
| Modelo: média ponderada pelo tempo. Padrão dos testes: neutro 5, meia-vida 30 dias,
| valor inicial com peso 2, peso 1 por observação, nível = 5 ± intensidade.
| "Agora" fixo em 2026-04-30 12:00.
*/

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-04-30 12:00:00'));

    // Linha de base dos testes (as chaves opcionais ficam desligadas; testes específicos as ligam).
    config([
        'proficiency.half_life_days' => 30.0,
        'proficiency.prior_weight' => 2.0,
        'proficiency.observation_weight' => 1.0,
        'proficiency.level_per_intensity' => 1.0,
        'proficiency.asymmetric_weights' => false,
        'proficiency.asymmetry_strength' => 1.0,
        'proficiency.reachable_extremes' => false,
        'proficiency.extreme_gain' => 1.8,
    ]);

    // A migration cadastra as 5 habilidades legadas; os testes usam habilidades próprias.
    Proficiency::query()->update(['is_active' => false]);
});

afterEach(fn () => Carbon::setTestNow());

/* ---------------------------------------------------------------------- */
/* Helpers (prefixo "pf")                                                  */
/* ---------------------------------------------------------------------- */

function pfUser(UserRole $role, array $attrs = []): User
{
    static $n = 0;
    $n++;

    return User::factory()->create(array_merge([
        'role' => $role->value,
        'username' => "pf-{$role->value}-{$n}",
        'name' => ucfirst($role->value)."_{$n}_",
        'status' => UserStatus::ACTIVE->value,
    ], $attrs));
}

function pfSkill(?string $name = null, bool $active = true): Proficiency
{
    static $n = 0;
    $n++;

    return Proficiency::create([
        'name' => $name ?? "Habilidade {$n}",
        'slug' => "habilidade-teste-{$n}",
        'is_active' => $active,
    ]);
}

function pfClassroom(array $teachers = []): Classroom
{
    static $n = 0;
    $n++;

    $room = Classroom::create(['name' => "Sala PF {$n}", 'grade' => ClassroomGrade::FIRST_YEAR->value]);
    foreach ($teachers as $t) {
        $room->teachers()->attach($t->id);
    }

    return $room;
}

function pfStudent(?Classroom $room = null, array $userAttrs = [], ?User $guardian = null): StudentSheet
{
    $guardian ??= pfUser(UserRole::GUARDIAN);
    $user = pfUser(UserRole::STUDENT, $userAttrs);

    return StudentSheet::create([
        'student_id' => $user->id,
        'guardian_id' => $guardian->id,
        'classroom_id' => $room?->id,
    ]);
}

function pfService(): ProficiencyService
{
    return app(ProficiencyService::class);
}

function pfRecord(User $teacher, StudentSheet $sheet, Proficiency $skill, string $at, float $intensity = 2.0, Dir $dir = Dir::POSITIVE, ?string $token = null): ProficiencyObservation
{
    return pfService()->record($teacher, $sheet, $skill, $dir, $intensity, 'Justificativa de teste', Carbon::parse($at), $token);
}

/** Valor calculado em um instante (padrão: agora). */
function pfValueAt(StudentSheet $sheet, Proficiency $skill, ?string $at = null): float
{
    return app(ProficiencyValueResolver::class)
        ->resolve([$sheet->id], $at ? Carbon::parse($at) : null, [$skill->id])[$sheet->id][$skill->id]['value'];
}

/** Valor em cache (student_proficiencies.value). */
function pfCached(StudentSheet $sheet, Proficiency $skill): float
{
    return (float) StudentProficiency::where('student_sheet_id', $sheet->id)
        ->where('proficiency_id', $skill->id)->value('value');
}

/** Professor + sala + aluno ativo + habilidade, o cenário mais comum. */
function pfScenario(): array
{
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $sheet = pfStudent($room);

    return [$teacher, $room, $sheet, pfSkill()];
}

/* ---------------------------------------------------------------------- */
/* Escala e níveis                                                         */
/* ---------------------------------------------------------------------- */

it('inicializa a proficiência no valor neutro e não duplica ao repetir o evento', function () {
    [, , $sheet, $skill] = pfScenario();

    pfService()->ensureInitialized($sheet);
    pfService()->ensureInitialized($sheet);

    $rows = StudentProficiency::where('student_sheet_id', $sheet->id)->where('proficiency_id', $skill->id)->get();

    expect($rows)->toHaveCount(1)
        ->and((float) $rows[0]->value)->toBe(5.0)
        ->and((float) $rows[0]->base_value)->toBe(5.0)
        ->and(pfValueAt($sheet, $skill))->toBe(5.0);
});

it('registra o nível como neutro ± intensidade e calcula a média ponderada', function () {
    [$teacher, , $sheet, $skill] = pfScenario();
    $other = pfSkill();

    $up = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0, Dir::POSITIVE);
    $down = pfRecord($teacher, $sheet, $other, '2026-04-01 10:00', 2.0, Dir::NEGATIVE);

    expect((float) $up->level)->toBe(7.0)
        ->and((float) $down->level)->toBe(3.0)
        // (2×5 + 1×7)/3 e (2×5 + 1×3)/3, lidos no instante do registro
        ->and(pfValueAt($sheet, $skill, '2026-04-01 10:00'))->toBe(5.6667)
        ->and(pfValueAt($sheet, $other, '2026-04-01 10:00'))->toBe(4.3333);
});

it('aceita intensidade fracionária e fator de nível menor que 1', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    config(['proficiency.level_per_intensity' => 0.5]);
    $obs = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0);

    expect((float) $obs->level)->toBe(6.0)
        ->and(pfValueAt($sheet, $skill, '2026-04-01 10:00'))->toBe(5.3333);

    config(['proficiency.level_per_intensity' => 1.0]);
    $b = pfSkill();
    $frac = pfRecord($teacher, $sheet, $b, '2026-04-01 10:00', 0.5);

    expect((float) $frac->level)->toBe(5.5);
});

it('limita o nível à escala e mantém o valor dentro de 0 a 10', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    config(['proficiency.level_per_intensity' => 3.0]);
    $obs = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 5.0);
    expect((float) $obs->level)->toBe(10.0);

    foreach (range(1, 20) as $i) {
        pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 5.0);
    }

    $value = pfValueAt($sheet, $skill, '2026-04-01 10:00');
    expect($value)->toBeGreaterThan(9.0)->and($value)->toBeLessThanOrEqual(10.0);
});

/* ---------------------------------------------------------------------- */
/* Tempo                                                                   */
/* ---------------------------------------------------------------------- */

it('usa peso fixo em todas as observações, qualquer que seja o intervalo entre elas', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    $a = pfRecord($teacher, $sheet, $skill, '2026-01-01 10:00');
    $b = pfRecord($teacher, $sheet, $skill, '2026-01-01 12:00');
    $c = pfRecord($teacher, $sheet, $skill, '2026-04-20 10:00');

    expect((float) $a->weight)->toBe(1.0)->and((float) $b->weight)->toBe(1.0)->and((float) $c->weight)->toBe(1.0);
});

it('dá mais valor ao que é recente na leitura', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    pfRecord($teacher, $sheet, $skill, '2026-03-01 10:00', 2.0, Dir::POSITIVE);
    pfRecord($teacher, $sheet, $skill, '2026-04-30 10:00', 2.0, Dir::NEGATIVE);

    // 60 dias depois: o positivo pesa 0,25 e o negativo 1,0 → abaixo do neutro.
    expect(pfValueAt($sheet, $skill, '2026-04-30 10:00'))->toBe(4.5385);
});

it('não depende da ordem de lançamento: observações retroativas são aceitas', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $skill = pfSkill();
    $chrono = pfStudent($room);
    $shuffled = pfStudent($room);

    pfRecord($teacher, $chrono, $skill, '2026-04-01 10:00', 2.0, Dir::POSITIVE);
    pfRecord($teacher, $chrono, $skill, '2026-04-08 10:00', 3.0, Dir::NEGATIVE);

    pfRecord($teacher, $shuffled, $skill, '2026-04-08 10:00', 3.0, Dir::NEGATIVE);
    pfRecord($teacher, $shuffled, $skill, '2026-04-01 10:00', 2.0, Dir::POSITIVE); // retroativa

    expect(ProficiencyObservation::where('student_sheet_id', $shuffled->id)->valid()->count())->toBe(2)
        ->and(pfValueAt($shuffled, $skill))->toBe(pfValueAt($chrono, $skill))
        ->and(pfCached($shuffled, $skill))->toBe(pfCached($chrono, $skill));
});

it('rejeita data futura sem alterar nada', function () {
    [$teacher, , $sheet, $skill] = pfScenario();
    $before = pfValueAt($sheet, $skill);

    expect(fn () => pfRecord($teacher, $sheet, $skill, '2026-06-01 10:00'))->toThrow(ValidationException::class);

    expect(ProficiencyObservation::count())->toBe(0)->and(pfValueAt($sheet, $skill))->toBe($before);
});

it('mantém as habilidades independentes entre si', function () {
    [$teacher, , $sheet, $a] = pfScenario();
    $b = pfSkill();

    pfRecord($teacher, $sheet, $a, '2026-04-01 10:00', 2.0, Dir::POSITIVE);
    pfRecord($teacher, $sheet, $b, '2026-04-01 10:00', 4.0, Dir::NEGATIVE);

    expect(pfValueAt($sheet, $a, '2026-04-01 10:00'))->toBe(5.6667)
        ->and(pfValueAt($sheet, $b, '2026-04-01 10:00'))->toBe(3.6667);
});

it('atualiza o cache na leitura de agora e o decaimento volta o valor ao neutro sem novas observações', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0);

    // Lido em 30/04 12:00 (29 dias e 2 h depois).
    expect(pfCached($sheet, $skill))->toBe(5.4068);

    // Um ano depois, sem novas observações, o valor praticamente voltou ao neutro.
    expect(abs(pfValueAt($sheet, $skill, '2027-04-30 12:00') - 5.0))->toBeLessThan(0.01);

    // refreshAll com outra data de leitura reflete o decaimento no cache.
    pfService()->refreshAll(Carbon::parse('2027-04-30 12:00'));
    expect(abs(pfCached($sheet, $skill) - 5.0))->toBeLessThan(0.01);
});

it('reduz a diferença causada pela frequência de registro (mesmo comportamento)', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $skill = pfSkill();

    $weekly = pfStudent($room);
    $monthly = pfStudent($room);

    $start = Carbon::parse('2025-10-01 00:00:00'); // 168 dias depois ainda é anterior ao "agora" do teste

    foreach (range(0, 167, 7) as $d) {
        pfRecord($teacher, $weekly, $skill, $start->copy()->addDays($d)->toDateTimeString(), 1.5);
    }
    foreach (range(0, 167, 30) as $d) {
        pfRecord($teacher, $monthly, $skill, $start->copy()->addDays($d)->toDateTimeString(), 1.5);
    }

    $at = $start->copy()->addDays(168)->toDateTimeString();

    expect(pfValueAt($weekly, $skill, $at))->toBe(6.1042)
        ->and(pfValueAt($monthly, $skill, $at))->toBe(5.5906);
});

/* ---------------------------------------------------------------------- */
/* Integridade                                                             */
/* ---------------------------------------------------------------------- */

it('impede duplicidade aluno + habilidade no banco', function () {
    [, , $sheet, $skill] = pfScenario();
    pfService()->ensureInitialized($sheet);

    expect(fn () => DB::table('student_proficiencies')->insert([
        'student_sheet_id' => $sheet->id,
        'proficiency_id' => $skill->id,
        'base_value' => 5, 'value' => 5,
    ]))->toThrow(QueryException::class);
});

it('mantém observação, cache e confiança consistentes após registrar', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    $obs = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0);

    $row = StudentProficiency::where('student_sheet_id', $sheet->id)->where('proficiency_id', $skill->id)->sole();

    expect((float) $row->value)->toBe(pfValueAt($sheet, $skill))
        ->and($row->observations_count)->toBe(1)
        ->and($row->last_observed_at->toDateTimeString())->toBe('2026-04-01 10:00:00')
        ->and($row->calculated_at)->not->toBeNull()
        ->and($obs->author_id)->toBe($teacher->id)
        ->and($obs->student_sheet_id)->toBe($sheet->id)
        ->and($obs->proficiency_id)->toBe($skill->id)
        ->and($obs->status)->toBe(ObservationStatus::VALID)
        // efeito no instante do registro: de 5,0 para 5,6667
        ->and((float) $obs->value_before)->toBe(5.0)
        ->and((float) $obs->value_after)->toBe(5.6667)
        ->and((float) $obs->applied_delta)->toBe(0.6667);
});

it('anula de forma auditável, recalcula sem a observação e recusa anular duas vezes', function () {
    [$teacher, , $sheet, $skill] = pfScenario();
    $coordinator = pfUser(UserRole::COORDINATOR);

    $first = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0);
    pfRecord($teacher, $sheet, $skill, '2026-04-08 10:00', 2.0);

    pfService()->void($first, $coordinator, 'Aluno errado');
    $first->refresh();

    expect($first->status)->toBe(ObservationStatus::VOIDED)
        ->and($first->voided_by)->toBe($coordinator->id)
        ->and($first->voided_at)->not->toBeNull()
        ->and($first->void_reason)->toBe('Aluno errado')
        ->and((float) $first->level)->toBe(7.0)               // nível original preservado
        ->and((float) $first->value_after)->toBe(5.6667)      // cálculo do registro preservado
        ->and(ProficiencyObservation::count())->toBe(2)       // nada é apagado
        ->and(pfCached($sheet, $skill))->toBe(5.4617);        // só a 2ª observação vale

    expect(fn () => pfService()->void($first, $coordinator, 'Outra vez'))->toThrow(ValidationException::class);
    expect(fn () => pfService()->void($first, $coordinator, '  '))->toThrow(ValidationException::class);
});

it('volta ao valor inicial quando todas as observações são anuladas', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    $obs = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0);
    pfService()->void($obs, $teacher, 'Engano');

    expect(pfCached($sheet, $skill))->toBe(5.0)
        ->and(StudentProficiency::where('student_sheet_id', $sheet->id)->value('observations_count'))->toBe(0);
});

it('é idempotente para o mesmo token de formulário', function () {
    [$teacher, , $sheet, $skill] = pfScenario();

    $a = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0, Dir::POSITIVE, 'token-123');
    $b = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 2.0, Dir::POSITIVE, 'token-123');

    expect($b->id)->toBe($a->id)->and(ProficiencyObservation::count())->toBe(1);
});

it('preserva o histórico ao inativar e bloqueia a exclusão física da habilidade', function () {
    [$teacher, , $sheet, $skill] = pfScenario();
    pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00');

    $skill->update(['is_active' => false]);

    expect(ProficiencyObservation::count())->toBe(1)
        ->and(pfValueAt($sheet, $skill))->toBeGreaterThan(5.0);

    expect(fn () => pfRecord($teacher, $sheet, $skill, '2026-04-05 10:00'))->toThrow(ValidationException::class);
    expect(fn () => $skill->delete())->toThrow(QueryException::class);
});

it('não permite avaliar aluno sem matrícula ativa nem intensidade fora dos limites', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $pending = pfStudent($room, ['status' => UserStatus::PENDING->value]);
    $skill = pfSkill();

    expect(fn () => pfRecord($teacher, $pending, $skill, '2026-04-01 10:00'))->toThrow(ValidationException::class);

    $active = pfStudent($room);
    expect(fn () => pfRecord($teacher, $active, $skill, '2026-04-01 10:00', 99.0))->toThrow(ValidationException::class);
    expect(fn () => pfRecord($teacher, $active, $skill, '2026-04-01 10:00', 0.0))->toThrow(ValidationException::class);
});

it('usa o valor inicial importado (base_value) como ponto de partida', function () {
    [$teacher, , $sheet, $skill] = pfScenario();
    pfService()->ensureInitialized($sheet);
    StudentProficiency::where('student_sheet_id', $sheet->id)->where('proficiency_id', $skill->id)
        ->update(['base_value' => 7, 'value' => 7]);

    expect(pfValueAt($sheet, $skill))->toBe(7.0);

    pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00', 1.0, Dir::NEGATIVE); // nível 4

    // (2×7 + 1×4) / 3 no instante do registro
    expect(pfValueAt($sheet, $skill, '2026-04-01 10:00'))->toBe(6.0);
});

/* ---------------------------------------------------------------------- */
/* Médias e snapshots                                                      */
/* ---------------------------------------------------------------------- */

it('calcula a média por habilidade entre os alunos e a atualiza a cada observação', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $skill = pfSkill();
    $a = pfStudent($room);
    $b = pfStudent($room);

    pfRecord($teacher, $a, $skill, '2026-04-30 11:00', 2.0);
    pfRecord($teacher, $b, $skill, '2026-04-30 11:00', 4.0);

    $expected = (pfValueAt($a, $skill) + pfValueAt($b, $skill)) / 2;
    $avg = ClassroomProficiencyAverage::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->sole();

    expect((float) $avg->average)->toEqualWithDelta($expected, 0.0002)->and($avg->students_count)->toBe(2);

    pfRecord($teacher, $a, $skill, '2026-04-30 11:30', 4.0, Dir::NEGATIVE);
    expect((float) $avg->refresh()->average)->toBeLessThan($expected);
});

it('atualiza a média após anulação', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $skill = pfSkill();
    $a = pfStudent($room);

    $obs = pfRecord($teacher, $a, $skill, '2026-04-30 10:00', 2.0);
    pfService()->void($obs, $teacher, 'Engano');

    $avg = ClassroomProficiencyAverage::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->sole();
    expect((float) $avg->average)->toBe(5.0);
});

it('usa null (e não 0) para sala sem alunos elegíveis e ignora alunos inativos', function () {
    $room = pfClassroom();
    $skill = pfSkill();

    app(ClassroomAverageService::class)->refresh($room->id);
    $empty = ClassroomProficiencyAverage::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->sole();

    expect($empty->average)->toBeNull()->and($empty->students_count)->toBe(0);

    $inactive = pfStudent($room, ['status' => UserStatus::INACTIVE->value]);
    pfService()->ensureInitialized($inactive);
    app(ClassroomAverageService::class)->refresh($room->id);

    expect($empty->refresh()->average)->toBeNull()->and($empty->students_count)->toBe(0);
});

it('recalcula as médias quando um aluno entra ou sai da sala', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $roomA = pfClassroom([$teacher]);
    $roomB = pfClassroom([$teacher]);
    $skill = pfSkill();
    $s1 = pfStudent($roomA);
    $s2 = pfStudent($roomA);

    pfRecord($teacher, $s1, $skill, '2026-04-30 11:00', 2.0);
    pfRecord($teacher, $s2, $skill, '2026-04-30 11:00', 4.0);

    $s2->update(['classroom_id' => $roomB->id]);
    app(ClassroomAverageService::class)->refreshMany([$roomA->id, $roomB->id]);

    $a = ClassroomProficiencyAverage::where('classroom_id', $roomA->id)->where('proficiency_id', $skill->id)->sole();
    $b = ClassroomProficiencyAverage::where('classroom_id', $roomB->id)->where('proficiency_id', $skill->id)->sole();

    expect((float) $a->average)->toEqualWithDelta(pfValueAt($s1, $skill), 0.0002)->and($a->students_count)->toBe(1)
        ->and((float) $b->average)->toEqualWithDelta(pfValueAt($s2, $skill), 0.0002)->and($b->students_count)->toBe(1);
});

it('gera snapshots pela data de referência, sem duplicar nem alterar os já gravados', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $skill = pfSkill();
    $student = pfStudent($room);
    $service = app(ClassroomAverageService::class);

    pfRecord($teacher, $student, $skill, '2026-04-01 10:00', 2.0);

    $day = Carbon::parse('2026-04-10');
    $created = $service->snapshot($day, $room->id);
    $again = $service->snapshot($day, $room->id);

    expect($created)->toBe(1)->and($again)->toBe(0);

    $snap = ClassroomProficiencySnapshot::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->sole();
    // lido no fim de 10/04 (9 dias e 14 h depois da observação)
    expect((float) $snap->average)->toBe(5.5721)->and($snap->students_count)->toBe(1);

    pfRecord($teacher, $student, $skill, '2026-04-20 10:00', 4.0, Dir::NEGATIVE);

    $service->snapshot($day, $room->id); // mesma data: não sobrescreve
    expect((float) $snap->fresh()->average)->toBe(5.5721);

    $service->snapshot(Carbon::parse('2026-04-25'), $room->id); // nova data: novo registro
    expect(ClassroomProficiencySnapshot::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->count())->toBe(2);
});

it('calcula o snapshot de uma data passada só com as observações até aquela data', function () {
    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $skill = pfSkill();
    $student = pfStudent($room);

    pfRecord($teacher, $student, $skill, '2026-04-01 10:00', 2.0);

    app(ClassroomAverageService::class)->snapshot(Carbon::parse('2026-03-20'), $room->id);

    $snap = ClassroomProficiencySnapshot::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->sole();
    expect((float) $snap->average)->toBe(5.0); // a observação de abril ainda não existia
});

it('gera snapshots e atualiza o cache pelo comando agendável', function () {
    $room = pfClassroom();
    pfSkill();

    $this->artisan('proficiency:snapshot', ['--date' => '2026-04-10'])->assertExitCode(0);
    $this->artisan('proficiency:refresh')->assertExitCode(0);

    expect(ClassroomProficiencySnapshot::where('classroom_id', $room->id)->count())->toBe(1);
});

/* ---------------------------------------------------------------------- */
/* Autorização                                                             */
/* ---------------------------------------------------------------------- */

it('permite observar apenas ao professor responsável pelo aluno', function () {
    [$teacher, , $sheet] = pfScenario();
    $outsider = pfUser(UserRole::TEACHER);

    expect(Gate::forUser($teacher)->allows('observe', $sheet))->toBeTrue()
        ->and(Gate::forUser($outsider)->allows('observe', $sheet))->toBeFalse()
        ->and(Gate::forUser(pfUser(UserRole::GUARDIAN))->allows('observe', $sheet))->toBeFalse()
        ->and(Gate::forUser($sheet->guardian)->allows('observe', $sheet))->toBeFalse()
        ->and(Gate::forUser(pfUser(UserRole::COORDINATOR))->allows('observe', $sheet))->toBeFalse()
        ->and(Gate::forUser(pfUser(UserRole::DIRECTOR))->allows('observe', $sheet))->toBeFalse();
});

it('restringe a anulação ao autor e ao coordenador', function () {
    [$teacher, $room, $sheet, $skill] = pfScenario();
    $obs = pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00');

    $colleague = pfUser(UserRole::TEACHER);
    $room->teachers()->attach($colleague->id);

    expect(Gate::forUser($teacher)->allows('void', $obs))->toBeTrue()
        ->and(Gate::forUser(pfUser(UserRole::COORDINATOR))->allows('void', $obs))->toBeTrue()
        ->and(Gate::forUser($colleague)->allows('void', $obs))->toBeFalse()
        ->and(Gate::forUser($sheet->guardian)->allows('void', $obs))->toBeFalse();

    pfService()->void($obs, $teacher, 'Engano');
    expect(Gate::forUser($teacher)->allows('void', $obs->refresh()))->toBeFalse();
});

it('limita a visualização: responsável vê valores do próprio filho, nunca o histórico', function () {
    [$teacher, , $sheet] = pfScenario();
    $guardian = $sheet->guardian;
    $otherGuardian = pfUser(UserRole::GUARDIAN);

    expect(Gate::forUser($guardian)->allows('viewProficiencies', $sheet))->toBeTrue()
        ->and(Gate::forUser($guardian)->allows('viewProficiencyHistory', $sheet))->toBeFalse()
        ->and(Gate::forUser($otherGuardian)->allows('viewProficiencies', $sheet))->toBeFalse()
        ->and(Gate::forUser($teacher)->allows('viewProficiencyHistory', $sheet))->toBeTrue()
        ->and(Gate::forUser(pfUser(UserRole::TEACHER))->allows('viewProficiencies', $sheet))->toBeFalse()
        ->and(Gate::forUser(pfUser(UserRole::COORDINATOR))->allows('viewProficiencyHistory', $sheet))->toBeTrue();
});

it('limita médias da sala à equipe responsável', function () {
    [$teacher, $room] = pfScenario();

    expect(Gate::forUser($teacher)->allows('viewProficiencies', $room))->toBeTrue()
        ->and(Gate::forUser(pfUser(UserRole::TEACHER))->allows('viewProficiencies', $room))->toBeFalse()
        ->and(Gate::forUser(pfUser(UserRole::GUARDIAN))->allows('viewProficiencies', $room))->toBeFalse()
        ->and(Gate::forUser(pfUser(UserRole::COORDINATOR))->allows('viewProficiencies', $room))->toBeTrue();
});

/* ---------------------------------------------------------------------- */
/* Cadastro de habilidades e integrações                                   */
/* ---------------------------------------------------------------------- */

it('permite ao coordenador cadastrar, editar e inativar habilidades sem perder histórico', function () {
    [$teacher, , $sheet] = pfScenario();
    $coordinator = pfUser(UserRole::COORDINATOR);

    $this->actingAs($coordinator)
        ->post(route('coordinator.proficiency.store'), ['name' => 'Cooperação', 'description' => 'Trabalha em grupo'])
        ->assertRedirect();

    $skill = Proficiency::where('name', 'Cooperação')->sole();

    // Aluno elegível já recebe a nova habilidade no valor neutro.
    expect(pfCached($sheet, $skill))->toBe(5.0);

    pfRecord($teacher, $sheet, $skill, '2026-04-01 10:00');

    $this->actingAs($coordinator)
        ->put(route('coordinator.proficiency.update', $skill), ['name' => 'Cooperação em grupo'])
        ->assertRedirect();

    $this->actingAs($coordinator)
        ->patch(route('coordinator.proficiency.status', $skill), ['is_active' => 0])
        ->assertRedirect();

    expect($skill->fresh()->name)->toBe('Cooperação em grupo')
        ->and($skill->fresh()->is_active)->toBeFalse()
        ->and(ProficiencyObservation::count())->toBe(1)
        ->and(pfCached($sheet, $skill))->toBeGreaterThan(5.0);

    $this->actingAs($coordinator)
        ->post(route('coordinator.proficiency.store'), ['name' => 'Cooperação em grupo'])
        ->assertSessionHasErrors('name');

    $this->actingAs($coordinator)->get(route('coordinator.proficiency.index'))->assertOk()->assertSee('Cooperação em grupo');
});

it('nega o cadastro de habilidades a professor, responsável e diretor', function () {
    $skill = pfSkill();

    foreach ([UserRole::TEACHER, UserRole::GUARDIAN, UserRole::DIRECTOR] as $role) {
        $user = pfUser($role);

        $this->actingAs($user)->post('/coordinator/proficiencies', ['name' => 'Intrusa'])->assertForbidden();
        $this->actingAs($user)->patch("/coordinator/proficiencies/{$skill->id}/status", ['is_active' => 0])->assertForbidden();
    }

    expect(Proficiency::where('name', 'Intrusa')->exists())->toBeFalse()
        ->and($skill->fresh()->is_active)->toBeTrue();
});

it('inicializa as proficiências e atualiza a média ao aprovar a matrícula', function () {
    $coordinator = pfUser(UserRole::COORDINATOR);
    $room = pfClassroom();
    $skill = pfSkill();
    $sheet = pfStudent($room, ['status' => UserStatus::PENDING->value]);
    Enrollment::create(['sheet_id' => $sheet->id]);

    expect(StudentProficiency::where('student_sheet_id', $sheet->id)->count())->toBe(0);

    $this->actingAs($coordinator)->put(route('coordinator.student.approve', ['enrollment' => $sheet->id]));

    expect(pfCached($sheet, $skill))->toBe(5.0);

    $avg = ClassroomProficiencyAverage::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->sole();
    expect((float) $avg->average)->toBe(5.0)->and($avg->students_count)->toBe(1);
});

it('atualiza as médias ao atribuir alunos à sala', function () {
    $coordinator = pfUser(UserRole::COORDINATOR);
    $room = pfClassroom();
    $skill = pfSkill();
    $sheet = pfStudent(null);

    $this->actingAs($coordinator)
        ->put(route('coordinator.classroom.students.update', $room), ['students' => [$sheet->student_id]])
        ->assertRedirect();

    $avg = ClassroomProficiencyAverage::where('classroom_id', $room->id)->where('proficiency_id', $skill->id)->sole();

    expect($sheet->fresh()->classroom_id)->toBe($room->id)
        ->and((float) $avg->average)->toBe(5.0)
        ->and($avg->students_count)->toBe(1);
});


/* ---------------------------------------------------------------------- */
/* Chaves opcionais, pré-visualização e avaliação em lote                  */
/* ---------------------------------------------------------------------- */

it('com pesos assimétricos ligados, o peso gravado depende do valor do aluno no registro', function () {
    config(['proficiency.asymmetric_weights' => true, 'proficiency.asymmetry_strength' => 1.0]);

    $teacher = pfUser(UserRole::TEACHER);
    $room = pfClassroom([$teacher]);
    $skill = pfSkill();

    $weightFor = function (float $startValue, Dir $dir) use ($teacher, $room, $skill) {
        $sheet = pfStudent($room);
        pfService()->ensureInitialized($sheet);
        StudentProficiency::where('student_sheet_id', $sheet->id)->where('proficiency_id', $skill->id)
            ->update(['base_value' => $startValue, 'value' => $startValue]);

        return (float) pfRecord($teacher, $sheet, $skill, '2026-04-30 11:00', 2.5, $dir)->weight;
    };

    expect($weightFor(5.0, Dir::POSITIVE))->toBe(1.0)
        ->and($weightFor(5.0, Dir::NEGATIVE))->toBe(1.0)
        ->and($weightFor(2.5, Dir::POSITIVE))->toBe(1.5)
        ->and($weightFor(2.5, Dir::NEGATIVE))->toBe(0.5)
        ->and($weightFor(7.5, Dir::POSITIVE))->toBe(0.5)
        ->and($weightFor(7.5, Dir::NEGATIVE))->toBe(1.5);
});

it('com a assimetria desligada o peso gravado é sempre o fixo', function () {
    [$teacher, , $sheet, $skill] = pfScenario();
    pfService()->ensureInitialized($sheet);
    StudentProficiency::where('student_sheet_id', $sheet->id)->where('proficiency_id', $skill->id)
        ->update(['base_value' => 2.5, 'value' => 2.5]);

    expect((float) pfRecord($teacher, $sheet, $skill, '2026-04-30 11:00', 2.5, Dir::POSITIVE)->weight)->toBe(1.0);
});

it('com extremos alcançáveis ligados, o valor exibido expande a distância do neutro', function () {
    config(['proficiency.reachable_extremes' => true, 'proficiency.extreme_gain' => 1.8]);
    [$teacher, , $sheet, $skill] = pfScenario();

    pfRecord($teacher, $sheet, $skill, '2026-04-30 11:00', 5.0, Dir::POSITIVE);

    // bruto (2×5 + 1×10)/3 = 6,6667 → exibido 5 + 1,8 × 1,6667 = 8,0 (lido no instante do registro)
    expect(pfValueAt($sheet, $skill, '2026-04-30 11:00'))->toBe(8.0);
});

it('a pré-visualização calcula exatamente o que o registro grava', function () {
    config([
        'proficiency.asymmetric_weights' => true, 'proficiency.asymmetry_strength' => 1.0,
        'proficiency.reachable_extremes' => true, 'proficiency.extreme_gain' => 1.8,
        'proficiency.prior_weight' => 1.0,
    ]);

    [$teacher, , $sheet, $skill] = pfScenario();
    pfRecord($teacher, $sheet, $skill, '2026-04-20 10:00', 5.0, Dir::POSITIVE);

    $preview = pfService()->previewEntry($sheet, $skill->id, -2.5);

    expect($preview)->not->toBeNull()
        ->and($preview['level'])->toBe(2.5)
        ->and($preview['after'])->toBeLessThan($preview['before']);

    // Pré-visualizar não grava nada.
    expect(ProficiencyObservation::count())->toBe(1);

    $saved = pfService()->record($teacher, $sheet, $skill, Dir::NEGATIVE, 2.5, 'Teste', now());

    expect((float) $saved->value_before)->toEqualWithDelta($preview['before'], 0.0001)
        ->and((float) $saved->value_after)->toEqualWithDelta($preview['after'], 0.0002)
        ->and((float) $saved->weight)->toEqualWithDelta($preview['weight'], 0.000001);
});

it('a pré-visualização devolve null para valor zero ou fora da faixa e funciona sem linha prévia', function () {
    [, , $sheet, $skill] = pfScenario();

    expect(pfService()->previewEntry($sheet, $skill->id, 0.0))->toBeNull()
        ->and(pfService()->previewEntry($sheet, $skill->id, 7.0))->toBeNull()
        ->and(pfService()->previewEntry($sheet, $skill->id, 0.05))->toBeNull();

    // Aluno ainda sem linha em student_proficiencies: parte do neutro.
    $preview = pfService()->previewEntry($sheet, $skill->id, 2.0);
    expect($preview['before'])->toBe(5.0)->and($preview['after'])->toBe(5.6667);
});

it('registra várias avaliações de uma vez com a mesma justificativa, de forma idempotente', function () {
    [$teacher, , $sheet, $a] = pfScenario();
    $b = pfSkill();
    $c = pfSkill();

    $entries = [
        ['proficiency_id' => $a->id, 'value' => 5.0],
        ['proficiency_id' => $b->id, 'value' => -2.5],
        ['proficiency_id' => $c->id, 'value' => 0.0], // sem avaliação: ignorada
    ];

    $saved = pfService()->recordBatch($teacher, $sheet, $entries, 'Participou bem hoje', 'tok-lote');
    $again = pfService()->recordBatch($teacher, $sheet, $entries, 'Participou bem hoje', 'tok-lote');

    expect($saved)->toHaveCount(2)
        ->and(ProficiencyObservation::count())->toBe(2)
        ->and(collect($saved)->pluck('reason')->unique()->all())->toBe(['Participou bem hoje'])
        ->and((float) $saved[0]->level)->toBe(10.0)
        ->and($saved[0]->direction)->toBe(Dir::POSITIVE)
        ->and((float) $saved[1]->level)->toBe(2.5)
        ->and($saved[1]->direction)->toBe(Dir::NEGATIVE)
        ->and(collect($again)->pluck('id')->all())->toBe(collect($saved)->pluck('id')->all());
});

it('o lote é tudo ou nada e exige ao menos uma avaliação', function () {
    [$teacher, , $sheet, $a] = pfScenario();
    $inactive = pfSkill(null, false);

    expect(fn () => pfService()->recordBatch($teacher, $sheet, [['proficiency_id' => $a->id, 'value' => 0.0]], 'x'))
        ->toThrow(ValidationException::class);

    // Uma habilidade inativa derruba o lote inteiro: nada é gravado.
    expect(fn () => pfService()->recordBatch($teacher, $sheet, [
        ['proficiency_id' => $a->id, 'value' => 3.0],
        ['proficiency_id' => $inactive->id, 'value' => 3.0],
    ], 'x'))->toThrow(ValidationException::class);

    expect(ProficiencyObservation::count())->toBe(0);
});

it('lê os botões e as chaves do config (.env)', function () {
    expect(config('proficiency.buttons.mild'))->toBe(2.5)
        ->and(config('proficiency.buttons.strong'))->toBe(5.0);
});
