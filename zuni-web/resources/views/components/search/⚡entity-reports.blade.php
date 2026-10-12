<?php

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Seção de Reports ATIVOS de uma entidade (aluno ou sala), usada nas páginas
 * de aluno e de sala:
 *   <livewire:search.entity-reports target="students"   :entityId="$student->studentSheet->id" />
 *   <livewire:search.entity-reports target="classrooms" :entityId="$classroom->id" />
 *
 * Para 'students' o id é de StudentSheet (não do User). Reports de alunos da sala
 * NÃO aparecem na seção da sala. A visibilidade vem de Report::visibleTo(), então
 * quem não pode ver a entidade simplesmente não recebe nenhum Report.
 */
new class extends Component
{
    use WithPagination;

    #[Locked]
    public string $target = 'students';

    #[Locked]
    public int $entityId = 0;

    public function isClassroom(): bool
    {
        return $this->target === 'classrooms';
    }

    public function canCreate(): bool
    {
        $user = Auth::user();

        if (! Gate::allows('create', Report::class)) {
            return false;
        }

        $service = app(ReportService::class);

        $query = $this->isClassroom()
            ? $service->classroomsFor($user)
            : $service->studentSheetsFor($user);

        return $query->whereKey($this->entityId)->exists();
    }

    #[Computed]
    public function reports()
    {
        $query = Report::query()
            ->active()
            ->visibleTo(Auth::user())
            ->with('author:id,name')
            ->latestFirst();

        $this->isClassroom()
            ? $query->forClassroom($this->entityId)
            : $query->forStudentSheet($this->entityId);

        return $query->paginate(5, pageName: 'reportsPage');
    }
};
?>

<div class="card bg-base-100 shadow">
    <div class="card-body">

        @php
            $prefix = auth()->user()->role->value;
            $createKey = $this->isClassroom() ? 'classrooms' : 'students';
        @endphp

        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="card-title">Relatórios</h2>

            <div class="flex flex-wrap gap-2">
                @if ($this->canCreate())
                    <a
                        href="{{ route($prefix . '.report.create', [$createKey => [$entityId]]) }}"
                        class="btn btn-sm bg-Csecondary text-white"
                    >
                        Criar relatório
                    </a>

                    @if ($this->isClassroom() && Route::has($prefix . '.classroom.students'))
                        <a
                            href="{{ route($prefix . '.classroom.students', $entityId) }}"
                            class="btn btn-sm btn-outline"
                        >
                            Criar para alunos da sala
                        </a>
                    @endif
                @endif
            </div>
        </div>

        @if ($this->isClassroom())
            <p class="text-xs text-base-content/50">
                Relatórios atribuídos diretamente à sala. Relatórios individuais dos alunos não aparecem aqui.
            </p>
        @endif

        <div class="mt-3 space-y-3">
            @forelse ($this->reports as $report)
                <a
                    wire:key="entity-report-{{ $report->id }}"
                    href="{{ route($prefix . '.report.show', $report) }}"
                    class="flex flex-col gap-1 rounded-box border border-base-300 p-3 hover:bg-base-200 transition"
                >
                    <span class="font-medium">{{ $report->title }}</span>
                    <span class="flex flex-wrap items-center gap-2 text-xs text-base-content/60">
                        <span class="badge badge-sm {{ $report->type->badgeClass() }}">{{ $report->type->label() }}</span>
                        {{ $report->author?->name ?? 'Autor removido' }}
                        · {{ $report->created_at->format('d/m/Y H:i') }}
                    </span>
                </a>
            @empty
                <p class="text-sm text-base-content/50">Nenhum relatório ativo.</p>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $this->reports->links() }}
        </div>

    </div>
</div>
