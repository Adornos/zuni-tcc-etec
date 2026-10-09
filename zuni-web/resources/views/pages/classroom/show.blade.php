@php
    $routePrefix = Auth::user()->role->value;
    $isCoordinator = Auth::user()->isCoordinator();
    $teachersTag = $isCoordinator ? 'a' : 'div';
@endphp

<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    <div class="container mx-auto col-span-4 row-span-4">

        <form
            action="{{ route('coordinator.classroom.update', $classroom) }}"
            method="POST"
            class="flex flex-col gap-6"
        >
            @csrf
            @method('PUT')

            {{-- DADOS DA TURMA --}}
            <div class="card bg-base-100 shadow-md">
                <div class="card-body p-6">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 mb-5 border-b border-base-200">

                        <div>
                            <h2 class="text-lg font-bold text-Cprimary">
                                {{ $classroom->name }}
                            </h2>
                            <p class="text-sm text-base-content/60 mt-1">
                                Turma #{{ str_pad($classroom->id, 4, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>

                        <span class="text-sm font-semibold {{ $classroom->status === 'active' ? 'text-success' : 'text-error' }}">
                            {{ $classroom->status === 'active' ? 'Ativa' : 'Inativa' }}
                        </span>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Nome --}}
                        <div class="form-control md:col-span-2">
                            <label class="label pb-1">
                                <span class="label-text text-base-content/60">Nome da turma</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $classroom->name) }}"
                                class="input input-bordered input-sm h-10 w-full"
                                required
                            >
                        </div>

                        {{-- Série --}}
                        <div class="form-control">
                            <label class="label pb-1">
                                <span class="label-text text-base-content/60">Série / Ano</span>
                            </label>
                            <select name="grade" class="select select-bordered select-sm h-10 w-full">
                                <option value="{{ old('name', $classroom->grade->value) }}">{{ old('name', $classroom->grade->label()) }}</option>

                                @foreach(App\Enums\ClassroomGrade::cases() as $grade)
                                    <option value="{{ $grade }}">{{ $grade->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Turno --}}
                        <div class="form-control">
                            <label class="label pb-1">
                                <span class="label-text text-base-content/60">Turno</span>
                            </label>
                            <select name="shift" class="select select-bordered select-sm h-10 w-full">
                                <option value="morning" @selected(old('shift', $classroom->shift) === 'morning')>Manhã</option>
                                <option value="afternoon" @selected(old('shift', $classroom->shift) === 'afternoon')>Tarde</option>
                                <option value="full_time" @selected(old('shift', $classroom->shift) === 'full_time')>Integral</option>
                                <option value="evening" @selected(old('shift', $classroom->shift) === 'evening')>Noite</option>
                            </select>
                        </div>

                        {{-- Capacidade --}}
                        <div class="form-control">
                            <label class="label pb-1">
                                <span class="label-text text-base-content/60">Capacidade</span>
                            </label>
                            <input
                                type="number"
                                name="capacity"
                                value="{{ old('capacity', $classroom->capacity) }}"
                                min="1"
                                class="input input-bordered input-sm h-10 w-full"
                            >
                        </div>

                        {{-- Status --}}
                        <div class="form-control">
                            <label class="label pb-1">
                                <span class="label-text text-base-content/60">Status</span>
                            </label>
                            <select name="status" class="select select-bordered select-sm h-10 w-full">
                                <option value="active" @selected(old('status', $classroom->status) === 'active')>Ativa</option>
                                <option value="inactive" @selected(old('status', $classroom->status) === 'inactive')>Inativa</option>
                            </select>
                        </div>

                    </div>

                </div>
            </div>


            {{-- CARD ÚNICO: PROFESSORES, ALUNOS E DESEMPENHO --}}
            <div class="card bg-base-100 shadow-md overflow-hidden">
                <div class="card-body p-0 gap-0">

                    {{-- PROFESSORES (clicável para o coordenador) --}}
                    <{{ $teachersTag }}
                        @if ($isCoordinator)
                            href="{{ route('coordinator.classroom.teachers', $classroom) }}"
                        @endif
                        class="block p-6 {{ $isCoordinator ? 'cursor-pointer transition-colors duration-200 hover:bg-base-200/60' : '' }}"
                    >

                        <div class="flex items-center justify-between gap-4">

                            <div class="min-w-0">
                                <h2 class="text-lg font-bold text-Cprimary">
                                    Professores
                                </h2>
                                <p class="text-sm text-base-content/60 mt-1 truncate">
                                    @if ($classroom->teachers->count())
                                        {{ $classroom->teachers->pluck('name')->join(', ') }}
                                    @else
                                        Nenhum professor atribuído.
                                    @endif
                                </p>
                            </div>

                            @if ($isCoordinator)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-Cprimary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            @endif

                        </div>

                    </{{ $teachersTag }}>


                    {{-- ALUNOS (clicável) --}}
                    <a
                        href="{{ route($routePrefix . '.classroom.students', $classroom) }}"
                        class="block p-6 border-t border-base-300 cursor-pointer transition-colors duration-200 hover:bg-base-200/60"
                    >

                        <div class="flex items-center justify-between gap-4">

                            <div>
                                <h2 class="text-lg font-bold text-Cprimary">
                                    Alunos
                                </h2>
                                <p class="text-sm text-base-content/60 mt-1">
                                    Matriculados: {{ $classroom->students->count() }} de {{ $classroom->capacity ?? '∞' }}
                                </p>
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-Cprimary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>

                        </div>

                    </a>


                    {{-- ÚLTIMO DESEMPENHO (clicável) --}}
                    <a
                        href="{{ route($routePrefix . '.report.create', $classroom) }}"
                        class="block p-6 border-t border-base-300 cursor-pointer transition-colors duration-200 hover:bg-base-200/60"
                    >

                        <div class="flex items-center justify-between gap-4">

                            <div>
                                <h2 class="text-lg font-bold text-Cprimary">
                                    Último desempenho
                                </h2>
                                <p class="text-sm text-base-content/60 mt-1">
                                    @if ($classroom->latestPerformance)
                                        Última avaliação: {{ $classroom->latestPerformance->year }} · {{ $classroom->latestPerformance->period->label() }}
                                    @else
                                        Essa turma ainda não possui uma avaliação de desempenho.
                                    @endif
                                </p>
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-Cprimary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>

                        </div>

                        @if ($classroom->latestPerformance)

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-x-6 gap-y-4 mt-5">

                                @foreach ([
                                    'Média' => $classroom->latestPerformance->average_grade,
                                    'Sociabilidade' => $classroom->latestPerformance->sociability,
                                    'Autonomia' => $classroom->latestPerformance->autonomy,
                                    'Engajamento' => $classroom->latestPerformance->engagement,
                                    'Comunicação' => $classroom->latestPerformance->communication,
                                ] as $label => $value)

                                    <div class="flex flex-col">
                                        <span class="text-sm text-base-content/60">{{ $label }}</span>
                                        <span class="text-2xl font-bold text-Cprimary">{{ $value }}</span>
                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </a>

                </div>
            </div>


            {{-- AÇÕES --}}
            @if ($isCoordinator)
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                    <a
                        href="{{ route('coordinator.classroom.index') }}"
                        class="btn btn-outline border-Cprimary text-Cprimary hover:bg-Cprimary hover:text-white w-full sm:w-auto"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn bg-Cprimary hover:bg-Cprimary/90 text-white border-none w-full sm:w-auto"
                    >
                        Salvar alterações
                    </button>

                </div>
            @endif

        </form>

    </div>

</x-dynamic-component>