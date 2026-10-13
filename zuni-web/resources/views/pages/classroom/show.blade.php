@php
    $routePrefix = Auth::user()->role->value;
    $isCoordinator = Auth::user()->isCoordinator();
@endphp


<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

<div class="space-y-6 col-span-4">

    {{-- ========================================================= --}}
    {{-- DADOS DA TURMA (somente leitura; a edição fica em outra tela) --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="card-title">{{ $classroom->name }}</h2>
                    <p class="text-sm text-base-content/60">
                        Turma #{{ str_pad($classroom->id, 4, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="badge rounded-full px-4 py-3 {{ $classroom->status === 'active' ? 'badge-success' : 'badge-error' }}">
                        {{ $classroom->status === 'active' ? 'Ativa' : 'Inativa' }}
                    </span>

                    @if ($isCoordinator)
                        <a href="{{ route('coordinator.classroom.edit', $classroom) }}" class="btn btn-sm btn-outline">
                            Editar turma
                        </a>
                    @endif
                </div>

            </div>

            <div class="divider"></div>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-sm uppercase text-base-content/60">Série / Ano</dt>
                    <dd class="mt-1 font-medium">{{ $classroom->grade?->label() ?? 'Não informada' }}</dd>
                </div>
                <div>
                    <dt class="text-sm uppercase text-base-content/60">Turno</dt>
                    <dd class="mt-1 font-medium">{{ $classroom->shift?->label() ?? 'Não informado' }}</dd>
                </div>
                <div>
                    <dt class="text-sm uppercase text-base-content/60">Capacidade</dt>
                    <dd class="mt-1 font-medium">{{ $classroom->capacity ?? 'Sem limite' }}</dd>
                </div>
                <div>
                    <dt class="text-sm uppercase text-base-content/60">Matriculados</dt>
                    <dd class="mt-1 font-medium">{{ $classroom->students->count() }}</dd>
                </div>
            </dl>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- PROFESSORES --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <div class="flex items-center justify-between">
                <h2 class="card-title">Professores</h2>

                @if ($isCoordinator)
                    <a href="{{ route('coordinator.classroom.teachers', $classroom) }}" class="btn btn-sm btn-outline">
                        Gerenciar
                    </a>
                @endif
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($classroom->teachers as $teacher)
                    <div class="flex items-center justify-between rounded-xl border border-base-200 p-3">
                        <div>
                            <p class="font-medium">{{ $teacher->name }}</p>
                            <p class="text-xs text-base-content/60">{{ $teacher->role->label() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50">Nenhum professor atribuído.</p>
                @endforelse
            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- ALUNOS --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <div class="flex items-center justify-between">
                <h2 class="card-title">Alunos</h2>

                <a href="{{ route($routePrefix . '.classroom.students', $classroom) }}" class="btn btn-sm btn-outline">
                    Gerenciar
                </a>
            </div>

            <p class="mt-2 text-sm text-base-content/50">
                Matriculados: {{ $classroom->students->count() }} de {{ $classroom->capacity ?? '∞' }}
            </p>

        </div>
    </div>

</div>

@can('viewProficiencies', $classroom)
    <div class="col-span-4">
        <livewire:search.classroom-proficiencies :classroomId="$classroom->id" />
    </div>
@endcan

<div class="col-span-4">
    <livewire:search.entity-reports target="classrooms" :entityId="$classroom->id" />
</div>

</x-dynamic-component>
