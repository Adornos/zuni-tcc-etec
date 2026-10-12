<?php

use App\Enums\ReportTarget;
use App\Enums\ReportType;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $target = '';
    public string $state = '';
    public bool $mine = false;

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'target', 'state', 'mine'], true)) {
            $this->resetPage();
        }
    }

    /** Professor e coordenador veem Reports inativos (professor: apenas os próprios). */
    public function canFilterState(): bool
    {
        $user = Auth::user();

        return $user->isTeacher() || $user->isCoordinator();
    }

    #[Computed]
    public function reports()
    {
        // Revalidado em toda renderização/requisição Livewire.
        abort_unless(Gate::allows('viewAny', Report::class), 403);

        $user = Auth::user();

        $query = Report::query()
            ->visibleTo($user)
            ->with([
                'author:id,name',
                'studentSheets.user:id,name',
                'classrooms:id,name',
            ]);

        // Professor: apenas os Reports que criou. Coordenador: todos, ou só os seus.
        if ($user->isTeacher() || ($user->isCoordinator() && $this->mine)) {
            $query->where('reports.author_id', $user->id);
        }

        if ($this->search !== '') {
            $like = '%'.addcslashes($this->search, '%_\\').'%';
            $query->where('reports.title', 'like', $like);
        }

        if ($type = ReportType::tryFrom($this->type)) {
            $query->where('reports.type', $type->value);
        }

        if ($target = ReportTarget::tryFrom($this->target)) {
            $query->where('reports.target', $target->value);
        }

        if ($this->canFilterState()) {
            $query->when($this->state === 'active', fn ($q) => $q->where('reports.is_active', true))
                ->when($this->state === 'inactive', fn ($q) => $q->where('reports.is_active', false));
        }

        return $query->latestFirst()->paginate(15);
    }
};
?>

<div>

    @php
        $prefix = auth()->user()->role->value;
    @endphp

    {{-- FILTROS --}}
    <div class="card bg-base-100 shadow-xl p-4 sm:p-5 mb-6">
        <h2 class="card-title mb-4 text-base sm:text-lg">Filtros</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

            <div class="form-control">
                <label class="label"><span class="label-text">Título</span></label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por título"
                    class="input input-bordered w-full"
                >
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Tipo</span></label>
                <select wire:model.live="type" class="select select-bordered w-full">
                    <option value="">Todos</option>
                    @foreach (ReportType::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Atribuído a</span></label>
                <select wire:model.live="target" class="select select-bordered w-full">
                    <option value="">Todos</option>
                    @foreach (ReportTarget::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>
            </div>

            @if ($this->canFilterState())
                <div class="form-control">
                    <label class="label"><span class="label-text">Estado</span></label>
                    <select wire:model.live="state" class="select select-bordered w-full">
                        <option value="">Todos</option>
                        <option value="active">Ativos</option>
                        <option value="inactive">Inativos</option>
                    </select>
                </div>
            @endif

        </div>

        @if (auth()->user()->isCoordinator())
            <label class="flex items-center gap-2 mt-4 cursor-pointer">
                <input type="checkbox" wire:model.live="mine" class="checkbox checkbox-primary checkbox-sm">
                <span class="text-sm">Somente os relatórios que eu criei</span>
            </label>
        @endif
    </div>

    {{-- RESULTADOS --}}
    <div class="flex flex-col gap-4">

        @forelse ($this->reports as $report)

            <div wire:key="report-{{ $report->id }}" class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 sm:p-5 gap-3">

                    <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                        <div class="min-w-0">
                            <a
                                href="{{ route($prefix . '.report.show', $report) }}"
                                class="text-lg font-semibold link link-hover"
                            >
                                {{ $report->title }}
                            </a>

                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="badge {{ $report->type->badgeClass() }}">{{ $report->type->label() }}</span>

                                @if (! auth()->user()->isDirector())
                                    <span class="badge {{ $report->is_active ? 'badge-success' : 'badge-error' }}">
                                        {{ $report->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                @endif

                                <span class="text-xs text-base-content/60">
                                    {{ $report->author?->name ?? 'Autor removido' }}
                                    · {{ $report->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>

                        <x-report.actions :report="$report" :compact="true" />
                    </div>

                    {{-- Resumo das atribuições --}}
                    @php
                        $names = $report->target === ReportTarget::STUDENTS
                            ? $report->studentSheets->map(fn ($s) => $s->user?->name)->filter()->values()
                            : $report->classrooms->pluck('name');
                    @endphp

                    <p class="text-sm text-base-content/70">
                        <span class="font-medium">{{ $report->target->label() }}:</span>
                        {{ $names->take(3)->join(', ') }}
                        @if ($names->count() > 3)
                            e mais {{ $names->count() - 3 }}
                        @endif
                    </p>

                </div>
            </div>

        @empty

            <div class="text-center py-10">
                <p class="text-base-content/60">Nenhum relatório encontrado.</p>
            </div>

        @endforelse

    </div>

    <div class="mt-6">
        {{ $this->reports->links() }}
    </div>

</div>
