<?php

use App\Enums\ClassroomGrade;
use App\Enums\ConversationType;
use App\Enums\ReportTarget;
use App\Enums\ReportType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Report;
use App\Models\StudentSheet;
use App\Models\User;
use App\Services\Chat\ChatPermissions;
use App\Services\Chat\ChatService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/* Helpers (prefixo "ch" para não colidir com outros testes) */

function chUser(UserRole $role, array $attrs = []): User
{
    static $n = 0;
    $n++;

    return User::factory()->create(array_merge([
        'role' => $role->value,
        'username' => "ch-{$role->value}-{$n}",
        'name' => ucfirst($role->value)." {$n}",
        'status' => UserStatus::ACTIVE->value,
    ], $attrs));
}

/** Responsável com um aluno (opcionalmente em uma sala). */
function chGuardian(?Classroom $classroom = null): User
{
    $guardian = chUser(UserRole::GUARDIAN);
    chStudent($guardian, $classroom);

    return $guardian;
}

function chStudent(User $guardian, ?Classroom $classroom = null): StudentSheet
{
    $student = chUser(UserRole::STUDENT);

    return StudentSheet::create([
        'student_id' => $student->id,
        'guardian_id' => $guardian->id,
        'classroom_id' => $classroom?->id,
    ]);
}

function chClassroom(array $teachers = []): Classroom
{
    static $n = 0;
    $n++;

    $classroom = Classroom::create(['name' => "Sala {$n}", 'grade' => ClassroomGrade::FIRST_YEAR->value]);
    foreach ($teachers as $teacher) {
        $classroom->teachers()->attach($teacher->id);
    }

    return $classroom;
}

function chService(): ChatService
{
    return app(ChatService::class);
}

function chReport(User $author, StudentSheet $sheet, bool $active = true): Report
{
    $report = Report::create([
        'title' => 'Relatório '.Str::random(4),
        'content' => 'Conteúdo',
        'type' => ReportType::NOTICE->value,
        'target' => ReportTarget::STUDENTS->value,
        'is_active' => $active,
        'author_id' => $author->id,
    ]);
    $report->studentSheets()->attach($sheet->id);

    return $report;
}

/* 1. Autorização */

it('permite professor↔coordenador e coordenador↔responsável', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $guardian = chGuardian();

    expect(chService()->start($teacher, $coordinator)->type)->toBe(ConversationType::TEACHER_COORDINATOR)
        ->and(chService()->start($guardian, $coordinator)->type)->toBe(ConversationType::COORDINATOR_GUARDIAN);
});

it('bloqueia professor com responsável no servidor', function () {
    $teacher = chUser(UserRole::TEACHER);
    $guardian = chGuardian();

    expect(app(ChatPermissions::class)->canChat($teacher, $guardian))->toBeFalse();
    chService()->start($teacher, $guardian);
})->throws(AuthorizationException::class);

it('bloqueia papéis incompatíveis, o próprio usuário, inativos e responsável sem aluno', function () {
    $permissions = app(ChatPermissions::class);
    $coordinator = chUser(UserRole::COORDINATOR);

    expect($permissions->canChat(chUser(UserRole::TEACHER), chUser(UserRole::TEACHER)))->toBeFalse()
        ->and($permissions->canChat(chGuardian(), chGuardian()))->toBeFalse()
        ->and($permissions->canChat($coordinator, $coordinator))->toBeFalse()
        ->and($permissions->canChat($coordinator, chUser(UserRole::TEACHER, ['status' => 'inactive'])))->toBeFalse()
        ->and($permissions->canChat($coordinator, chUser(UserRole::GUARDIAN)))->toBeFalse();
});

it('exige autenticação nas rotas do chat', function () {
    $this->get(route('teacher.chat'))->assertRedirect();
    $this->get(route('chat.link', 1))->assertRedirect();
});

it('impede acesso à conversa de terceiros', function () {
    $conversation = chService()->start(chUser(UserRole::TEACHER), $coordinator = chUser(UserRole::COORDINATOR));
    $intruder = chUser(UserRole::COORDINATOR);

    expect($intruder->can('view', $conversation))->toBeFalse();
    chService()->send($intruder, $conversation, 'oi');
})->throws(AuthorizationException::class);

it('não lista conversas de outros no inbox', function () {
    $coordinator = chUser(UserRole::COORDINATOR);
    chService()->start(chUser(UserRole::TEACHER), $coordinator);
    $other = chUser(UserRole::COORDINATOR);

    expect(chService()->inbox($other)->count())->toBe(0);
});

/* 2. Criação e mensagens */

it('reaproveita a conversa existente (sem duplicar)', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);

    $a = chService()->start($teacher, $coordinator);
    $b = chService()->start($coordinator, $teacher);

    expect($a->id)->toBe($b->id)
        ->and(Conversation::count())->toBe(1)
        ->and($a->participants()->count())->toBe(2);
});

it('envia mensagem válida e preserva quebras de linha', function () {
    $teacher = chUser(UserRole::TEACHER);
    $conversation = chService()->start($teacher, chUser(UserRole::COORDINATOR));

    $message = chService()->send($teacher, $conversation, "linha 1\r\nlinha 2");

    expect($message->body)->toBe("linha 1\nlinha 2")
        ->and($conversation->fresh()->last_message_id)->toBe($message->id);
});

it('rejeita conteúdo vazio e excessivo', function (string $body) {
    $teacher = chUser(UserRole::TEACHER);
    $conversation = chService()->start($teacher, chUser(UserRole::COORDINATOR));

    chService()->send($teacher, $conversation, $body);
})->with([
    'vazio' => '   ',
    'excessivo' => fn () => str_repeat('a', config('chat.max_length') + 1),
])->throws(ValidationException::class);

it('guarda HTML como texto e escapa na saída', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    chService()->send($teacher, $conversation, '<script>alert(1)</script>');

    Livewire::actingAs($coordinator)
        ->withQueryParams(['c' => $conversation->id])
        ->test('chat.inbox')
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
});

it('é idempotente por client_uuid (reenvio não duplica)', function () {
    $teacher = chUser(UserRole::TEACHER);
    $conversation = chService()->start($teacher, chUser(UserRole::COORDINATOR));
    $uuid = (string) Str::uuid();

    $first = chService()->send($teacher, $conversation, 'oi', $uuid);
    $second = chService()->send($teacher, $conversation, 'oi', $uuid);

    expect($second->id)->toBe($first->id)->and(Message::count())->toBe(1);
});

it('bloqueia envio quando o outro participante foi desativado', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    chService()->send($teacher, $conversation, 'antes');

    $coordinator->forceFill(['status' => 'inactive'])->save();

    expect(fn () => chService()->send($teacher, $conversation, 'depois'))->toThrow(AuthorizationException::class)
        ->and(Message::count())->toBe(1);
});

/* 3. Histórico e pesquisa */

it('pagina o histórico em ordem cronológica via componente', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    foreach (range(1, 35) as $i) {
        chService()->send($teacher, $conversation, "msg-{$i}-fim");
    }

    $component = Livewire::actingAs($coordinator)
        ->withQueryParams(['c' => $conversation->id])
        ->test('chat.inbox')
        ->assertSee('msg-35-fim')
        ->assertDontSee('msg-5-fim')
        ->assertSee('Carregar mensagens anteriores');

    $component->call('loadOlder')->assertSee('msg-5-fim');
});

it('pesquisa mensagens somente nas conversas do usuário', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    chService()->send($teacher, $conversation, 'segredo-banana');

    $other = chUser(UserRole::COORDINATOR);
    $otherTeacher = chUser(UserRole::TEACHER);
    chService()->send($otherTeacher, chService()->start($otherTeacher, $other), 'segredo-banana alheio');

    $hits = chService()->searchMessages($teacher, 'banana');

    expect($hits->total())->toBe(1)->and($hits->first()->conversation_id)->toBe($conversation->id);
});

it('pesquisa conversas por nome e mantém arquivadas fora da caixa principal', function () {
    $teacher = chUser(UserRole::TEACHER, ['name' => 'Professora Violeta']);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);

    expect(chService()->inbox($coordinator, null, 'Violeta')->count())->toBe(1)
        ->and(chService()->inbox($coordinator, null, 'Inexistente')->count())->toBe(0);

    chService()->archive($coordinator, $conversation);

    expect(chService()->inbox($coordinator)->count())->toBe(0)
        ->and(chService()->inbox($coordinator, null, '', true)->count())->toBe(1)
        ->and(chService()->inbox($teacher)->count())->toBe(1);
});

/* 4. Leitura e entrega */

it('controla não lidas, entrega e leitura de forma idempotente', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    $m1 = chService()->send($teacher, $conversation, 'um');
    $m2 = chService()->send($teacher, $conversation, 'dois');

    $unread = fn () => (int) chService()->inbox($coordinator)->first()->unread_count;
    expect($unread())->toBe(2);

    chService()->markDelivered($coordinator);
    chService()->markDelivered($coordinator);
    $pivot = $conversation->participants()->where('user_id', $coordinator->id)->first();
    expect($pivot->last_delivered_message_id)->toBe($m2->id)->and($pivot->last_read_message_id)->toBeNull();

    chService()->markRead($coordinator, $conversation, $m1->id);
    expect($unread())->toBe(1);

    chService()->markRead($coordinator, $conversation);
    chService()->markRead($coordinator, $conversation);
    expect($unread())->toBe(0)
        ->and(chService()->inbox($teacher)->first()->unread_count)->toBe(0);
});

it('mensagem nova desarquiva para os dois lados', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    chService()->archive($coordinator, $conversation);

    chService()->send($teacher, $conversation, 'voltei');

    expect(chService()->inbox($coordinator)->count())->toBe(1);
});

/* 5. Arquivamento e encerramento */

it('somente coordenador encerra; encerrada bloqueia envio e preserva histórico; reabrir libera', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    chService()->send($teacher, $conversation, 'ola');

    expect(fn () => chService()->close($teacher, $conversation))->toThrow(AuthorizationException::class);

    chService()->close($coordinator, $conversation);
    expect(fn () => chService()->send($teacher, $conversation->fresh(), 'x'))->toThrow(ValidationException::class)
        ->and(Message::count())->toBe(1);

    chService()->reopen($coordinator, $conversation->fresh());
    chService()->send($teacher, $conversation->fresh(), 'de novo');
    expect(Message::count())->toBe(2);
});

/* 6. Links internos */

it('compartilha relatório e aluno acessíveis aos dois e abre pela rota segura', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $classroom = chClassroom([$teacher]);
    $sheet = chStudent(chUser(UserRole::GUARDIAN), $classroom);
    $report = chReport($coordinator, $sheet);
    $conversation = chService()->start($teacher, $coordinator);

    $message = chService()->send($teacher, $conversation, 'veja', null, ['type' => 'report', 'id' => $report->id]);
    $this->actingAs($coordinator)->get(route('chat.link', $message))
        ->assertRedirect(route('coordinator.report.show', $report));

    $student = chService()->send($teacher, $conversation, 'aluno', null, ['type' => 'student', 'id' => $sheet->id]);
    $this->actingAs($teacher)->get(route('chat.link', $student))
        ->assertRedirect(route('teacher.student.show', $sheet->user));
});

it('bloqueia link para recurso que o destinatário não pode abrir', function () {
    $coordinator = chUser(UserRole::COORDINATOR);
    $guardian = chGuardian();
    $otherSheet = chStudent(chUser(UserRole::GUARDIAN));
    $conversation = chService()->start($coordinator, $guardian);

    expect(fn () => chService()->send($coordinator, $conversation, 'x', null, ['type' => 'student', 'id' => $otherSheet->id]))
        ->toThrow(ValidationException::class);
});

it('rejeita tipos de link inválidos e protocolos maliciosos', function () {
    $teacher = chUser(UserRole::TEACHER);
    $conversation = chService()->start($teacher, chUser(UserRole::COORDINATOR));

    foreach (['javascript:alert(1)', 'http://evil.test', 'user'] as $type) {
        expect(fn () => chService()->send($teacher, $conversation, 'x', null, ['type' => $type, 'id' => 1]))
            ->toThrow(ValidationException::class);
    }

    expect(Message::count())->toBe(0);
});

it('nega abertura de link a quem não participa ou perdeu acesso', function () {
    $coordinator = chUser(UserRole::COORDINATOR);
    $guardian = chUser(UserRole::GUARDIAN);
    $sheet = chStudent($guardian);
    $report = chReport($coordinator, $sheet);
    $conversation = chService()->start($coordinator, $guardian);
    $message = chService()->send($coordinator, $conversation, 'relatório', null, ['type' => 'report', 'id' => $report->id]);

    $this->actingAs(chUser(UserRole::COORDINATOR))->get(route('chat.link', $message))->assertForbidden();

    $this->actingAs($guardian)->get(route('chat.link', $message))->assertRedirect();

    $report->update(['is_active' => false]);
    $this->actingAs($guardian)->get(route('chat.link', $message))->assertForbidden();
});

/* 7. Atualização (polling) */

it('o polling marca entrega e leitura apenas para quem tem a conversa aberta', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);
    $message = chService()->send($teacher, $conversation, 'ping');

    Livewire::actingAs($coordinator)->test('chat.inbox')->call('poll');
    $pivot = $conversation->participants()->where('user_id', $coordinator->id)->first();
    expect($pivot->last_delivered_message_id)->toBe($message->id)->and($pivot->last_read_message_id)->toBeNull();

    Livewire::actingAs($coordinator)->withQueryParams(['c' => $conversation->id])->test('chat.inbox')->call('poll');
    expect($conversation->participants()->where('user_id', $coordinator->id)->first()->last_read_message_id)->toBe($message->id);
});

/* 8. Interface */

it('mostra as categorias só para o coordenador e restringe destinatários', function () {
    $coordinator = chUser(UserRole::COORDINATOR);
    $teacher = chUser(UserRole::TEACHER, ['name' => 'Prof Alfa']);
    $guardian = chGuardian();

    Livewire::actingAs($coordinator)->test('chat.inbox')
        ->assertSee('Corpo docente')->assertSee('Responsáveis');

    Livewire::actingAs($teacher)->test('chat.inbox')
        ->assertDontSee('Responsáveis')
        ->call('toggleStarting')
        ->assertSee($coordinator->name)
        ->assertDontSee($guardian->name);

    Livewire::actingAs($guardian)->test('chat.inbox')
        ->call('toggleStarting')
        ->assertSee($coordinator->name)
        ->assertDontSee('Prof Alfa');
});

it('impede iniciar conversa com contato não permitido via componente', function () {
    $teacher = chUser(UserRole::TEACHER);
    $guardian = chGuardian();

    Livewire::actingAs($teacher)->test('chat.inbox')->call('startWith', $guardian->id)->assertHasErrors('start');

    expect(Conversation::count())->toBe(0);
});

it('envia pelo componente e mostra erro de validação', function () {
    $teacher = chUser(UserRole::TEACHER);
    $coordinator = chUser(UserRole::COORDINATOR);
    $conversation = chService()->start($teacher, $coordinator);

    Livewire::actingAs($teacher)->withQueryParams(['c' => $conversation->id])->test('chat.inbox')
        ->call('send', (string) Str::uuid(), 'Olá, coordenação')
        ->assertSee('Olá, coordenação')
        ->call('send', (string) Str::uuid(), '   ')
        ->assertHasErrors('body');
});

it('não abre conversa alheia manipulando o parâmetro da URL', function () {
    $conversation = chService()->start(chUser(UserRole::TEACHER), chUser(UserRole::COORDINATOR));
    chService()->send($conversation->initiator, $conversation, 'privado-xyz');

    Livewire::actingAs(chUser(UserRole::COORDINATOR))->withQueryParams(['c' => $conversation->id])
        ->test('chat.inbox')->assertDontSee('privado-xyz');
});

it('mostra aviso ao diretor na página de chat e não permite o componente', function () {
    $director = chUser(UserRole::DIRECTOR);

    $this->actingAs($director)->get(route('director.chat'))->assertOk()->assertSee('professores, coordenadores e responsáveis');

    Livewire::actingAs($director)->test('chat.inbox')->assertForbidden();
});
