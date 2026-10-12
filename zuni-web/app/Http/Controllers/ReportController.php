<?php

namespace App\Http\Controllers;

use App\Enums\ReportTarget;
use App\Enums\ReportType;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports)
    {
    }

    /**
     * Listagem geral (componente Livewire search.report-list).
     */
    public function index()
    {
        Gate::authorize('viewAny', Report::class);

        return view('pages.report.index');
    }

    /**
     * Formulário de criação. Aceita contexto opcional por query string:
     *   ?students[]=<student_sheet_id>&students[]=...
     *   ?classrooms[]=<classroom_id>
     * O contexto é revalidado (existência + autorização); se inválido, 403.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Report::class);

        $context = $this->resolveContext($request);

        return view('pages.report.create', $this->formData(
            report: null,
            target: $context['target'],
            selected: $context['ids'],
        ));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Report::class);

        $data = $this->validatePayload($request);

        $report = $this->reports->create(Auth::user(), $data);

        return redirect()
            ->route($this->prefix().'.report.show', $report)
            ->with('success', 'Relatório criado com sucesso!');
    }

    public function show(Report $report)
    {
        Gate::authorize('view', $report);

        /** @var User $user */
        $user = Auth::user();

        $report->load('author:id,name');

        if ($report->target === ReportTarget::STUDENTS) {
            $entities = $report->studentSheets()
                ->with(['user:id,name', 'classroom:id,name'])
                // O responsável só enxerga os próprios filhos, nunca os demais alunos do relatório.
                ->when($user->isGuardian(), fn ($q) => $q->where('student_sheets.guardian_id', $user->id))
                ->get()
                ->sortBy('user.name')
                ->values();
        } else {
            $entities = $report->classrooms()->orderBy('classrooms.name')->get();
        }

        return view('pages.report.show', [
            'report' => $report,
            'entities' => $entities,
            'prefix' => $this->prefix(),
        ]);
    }

    public function edit(Report $report)
    {
        Gate::authorize('update', $report);

        $selected = $report->target === ReportTarget::STUDENTS
            ? $report->studentSheets()->pluck('student_sheets.id')->all()
            : $report->classrooms()->pluck('classrooms.id')->all();

        return view('pages.report.edit', $this->formData(
            report: $report,
            target: $report->target,
            selected: $selected,
        ));
    }

    public function update(Request $request, Report $report)
    {
        Gate::authorize('update', $report);

        $data = $this->validatePayload($request, $report);

        $this->reports->update($report, Auth::user(), $data);

        return redirect()
            ->route($this->prefix().'.report.show', $report)
            ->with('success', 'Relatório alterado com sucesso!');
    }

    /**
     * Exclusão definitiva (somente autor). Não substitui a inativação.
     */
    public function destroy(Report $report)
    {
        Gate::authorize('delete', $report);

        $this->reports->delete($report);

        return redirect()
            ->route($this->prefix().'.report.index')
            ->with('success', 'Relatório apagado com sucesso!');
    }

    /**
     * Inativa/reativa sem tocar em conteúdo, autor ou atribuições.
     * Autor ou coordenador (ver ReportPolicy::toggleActive).
     */
    public function status(Request $request, Report $report)
    {
        Gate::authorize('toggleActive', $report);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $active = (bool) $validated['is_active'];

        $this->reports->setActive($report, $active);

        return back()->with(
            'success',
            $active ? 'Relatório reativado com sucesso!' : 'Relatório inativado com sucesso!'
        );
    }

    /* ----------------------------------------------------------------- */
    /* Auxiliares                                                         */
    /* ----------------------------------------------------------------- */

    private function prefix(): string
    {
        return Auth::user()->role->value;
    }

    /**
     * Valida os campos do formulário. Autor e estado nunca são lidos do request.
     * Na edição, a categoria é fixa e precisa coincidir com a do relatório.
     *
     * @return array{title:string, content:string, type:ReportType, target:ReportTarget, ids:array<int,mixed>}
     */
    private function validatePayload(Request $request, ?Report $report = null): array
    {
        $target = $request->input('target');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:20000'],
            'type' => ['required', Rule::enum(ReportType::class)],
            'target' => $report
                ? ['required', Rule::in([$report->target->value])]
                : ['required', Rule::enum(ReportTarget::class)],

            'students' => [
                Rule::requiredIf($target === ReportTarget::STUDENTS->value),
                Rule::prohibitedIf($target !== ReportTarget::STUDENTS->value),
                'array',
            ],
            'students.*' => ['integer', 'distinct', Rule::exists('student_sheets', 'id')],

            'classrooms' => [
                Rule::requiredIf($target === ReportTarget::CLASSROOMS->value),
                Rule::prohibitedIf($target !== ReportTarget::CLASSROOMS->value),
                'array',
            ],
            'classrooms.*' => ['integer', 'distinct', Rule::exists('classrooms', 'id')],
        ];

        $messages = [
            'target.in' => 'Não é permitido mudar a categoria de atribuição do relatório.',
            'students.required' => 'Selecione ao menos um aluno.',
            'classrooms.required' => 'Selecione ao menos uma sala.',
            'students.prohibited' => 'Um relatório não pode ser atribuído a alunos e salas ao mesmo tempo.',
            'classrooms.prohibited' => 'Um relatório não pode ser atribuído a alunos e salas ao mesmo tempo.',
        ];

        $attributes = [
            'title' => 'título',
            'content' => 'corpo',
            'type' => 'tipo',
            'target' => 'categoria',
            'students' => 'alunos',
            'classrooms' => 'salas',
        ];

        $validated = $request->validate($rules, $messages, $attributes);

        $targetEnum = ReportTarget::from($validated['target']);

        return [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => ReportType::from($validated['type']),
            'target' => $targetEnum,
            'ids' => $targetEnum === ReportTarget::STUDENTS
                ? ($validated['students'] ?? [])
                : ($validated['classrooms'] ?? []),
        ];
    }

    /**
     * Lê e valida o contexto (query string) da página de criação.
     *
     * @return array{target: ?ReportTarget, ids: array<int,int>}
     */
    private function resolveContext(Request $request): array
    {
        $students = $this->cleanIds($request->query('students'));
        $classrooms = $this->cleanIds($request->query('classrooms'));

        if ($students !== [] && $classrooms !== []) {
            abort(422, 'Contexto inválido: não é possível combinar alunos e salas.');
        }

        if ($students === [] && $classrooms === []) {
            return ['target' => null, 'ids' => []];
        }

        $target = $students !== [] ? ReportTarget::STUDENTS : ReportTarget::CLASSROOMS;

        try {
            $ids = $this->reports->resolveAssignments(
                Auth::user(),
                $target,
                $students !== [] ? $students : $classrooms
            );
        } catch (ValidationException) {
            abort(403, 'Contexto não autorizado para criação de relatório.');
        }

        return ['target' => $target, 'ids' => $ids];
    }

    /**
     * @return array<int,int>
     */
    private function cleanIds(mixed $value): array
    {
        return collect((array) $value)
            ->filter(fn ($v) => is_scalar($v) && ctype_digit((string) $v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Dados do formulário. Na edição, entidades já vinculadas são incluídas
     * mesmo que o autor tenha perdido a responsabilidade sobre elas, para que
     * ele consiga desmarcá-las (o servidor só aceita um conjunto final autorizado).
     */
    private function formData(?Report $report, ?ReportTarget $target, array $selected): array
    {
        /** @var User $user */
        $user = Auth::user();

        $sheets = $this->reports->studentSheetsFor($user)
            ->with(['user:id,name', 'classroom:id,name'])
            ->get();

        $classrooms = $this->reports->classroomsFor($user)
            ->orderBy('classrooms.name')
            ->get(['classrooms.id', 'classrooms.name']);

        if ($report) {
            if ($target === ReportTarget::STUDENTS) {
                $extra = $report->studentSheets()
                    ->whereNotIn('student_sheets.id', $sheets->pluck('id'))
                    ->with(['user:id,name', 'classroom:id,name'])
                    ->get();
                $sheets = $sheets->concat($extra);
            } else {
                $extra = $report->classrooms()
                    ->whereNotIn('classrooms.id', $classrooms->pluck('id'))
                    ->get(['classrooms.id', 'classrooms.name']);
                $classrooms = $classrooms->concat($extra);
            }
        }

        return [
            'report' => $report,
            'target' => $target,
            'selected' => $selected,
            'sheets' => $sheets->sortBy('user.name')->values(),
            'classrooms' => $classrooms->sortBy('name')->values(),
            'types' => ReportType::cases(),
            'prefix' => $this->prefix(),
        ];
    }
}
