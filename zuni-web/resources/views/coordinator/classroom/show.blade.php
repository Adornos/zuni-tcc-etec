<x-panel.coordinator>

<form
    action="{{ route('coordinator.classroom.update', $classroom) }}"
    method="POST"
    class="space-y-6 col-span-4"
>
    @csrf
    @method('PUT')


    {{-- ========================================================= --}}
    {{-- DADOS DA TURMA --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="card-title">
                        {{ $classroom->name }}
                    </h2>

                    <p class="text-sm text-base-content/60">
                        Turma #{{ str_pad($classroom->id, 4, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <span class="
                    badge
                    rounded-full
                    px-4
                    py-3
                    {{ $classroom->status === 'active'
                        ? 'badge-success'
                        : 'badge-error'
                    }}
                ">
                    {{ $classroom->status === 'active' ? 'Ativa' : 'Inativa' }}
                </span>

            </div>


            <div class="divider"></div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Nome --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">
                            Nome da turma
                        </span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $classroom->name) }}"
                        class="input input-bordered w-full"
                        required
                    >
                </div>


                {{-- Série --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Série / Ano
                        </span>
                    </label>

                    <input
                        type="text"
                        name="grade"
                        value="{{ old('grade', $classroom->grade) }}"
                        class="input input-bordered w-full"
                        required
                    >
                </div>


                {{-- Turno --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Turno
                        </span>
                    </label>

                    <select
                        name="shift"
                        class="select select-bordered w-full"
                    >
                        <option value="morning"
                            @selected(old('shift', $classroom->shift) === 'morning')>
                            Manhã
                        </option>

                        <option value="afternoon"
                            @selected(old('shift', $classroom->shift) === 'afternoon')>
                            Tarde
                        </option>

                        <option value="full_time"
                            @selected(old('shift', $classroom->shift) === 'full_time')>
                            Integral
                        </option>

                        <option value="evening"
                            @selected(old('shift', $classroom->shift) === 'evening')>
                            Noite
                        </option>
                    </select>
                </div>


                {{-- Capacidade --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Capacidade
                        </span>
                    </label>

                    <input
                        type="number"
                        name="capacity"
                        value="{{ old('capacity', $classroom->capacity) }}"
                        min="1"
                        class="input input-bordered w-full"
                    >
                </div>


                {{-- Status --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Status
                        </span>
                    </label>

                    <select
                        name="status"
                        class="select select-bordered w-full"
                    >
                        <option value="active"
                            @selected(old('status', $classroom->status) === 'active')>
                            Ativa
                        </option>

                        <option value="inactive"
                            @selected(old('status', $classroom->status) === 'inactive')>
                            Inativa
                        </option>
                    </select>
                </div>

            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- PROFESSORES --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <div class="flex items-center justify-between">

                <h2 class="card-title">
                    Professores
                </h2>

                <a
                    href="{{ route('coordinator.classroom.teachers', $classroom) }}"
                    class="btn btn-sm btn-outline"
                >
                    Gerenciar
                </a>

            </div>


            <div class="mt-4 space-y-3">

                @forelse($classroom->teachers as $teacher)

                    <div class="
                        flex
                        items-center
                        justify-between
                        rounded-xl
                        border
                        border-base-200
                        p-3
                    ">

                        <div>
                            <p class="font-medium">
                                {{ $teacher->name }}
                            </p>

                            <p class="text-xs text-base-content/60">
                                {{ $teacher->role->label() }}
                            </p>
                        </div>

                    </div>

                @empty

                    <p class="text-sm text-base-content/50">
                        Nenhum professor atribuído.
                    </p>

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

                <h2 class="card-title">
                    Alunos
                </h2>

                <a
                    href="{{ route('coordinator.classroom.students', $classroom) }}"
                    class="btn btn-sm btn-outline"
                >
                    Gerenciar
                </a>

            </div>


            <div class="stats shadow mt-4">

                <div class="stat">
                    <div class="stat-title">
                        Alunos matriculados
                    </div>

                    <div class="stat-value">
                        {{ $classroom->students->count() }}
                    </div>

                    <div class="stat-desc">
                        de {{ $classroom->capacity ?? '∞' }}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- ÚLTIMO DESEMPENHO --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">

        <div class="card-body">

            <div class="flex items-center justify-between">

                <h2 class="card-title">
                    Último desempenho
                </h2>

                <a
                    href="{{ route('coordinator.report.create', $classroom) }}"
                    class="btn btn-primary btn-sm"
                >
                    Criar relatório
                </a>

            </div>


            @if($classroom->latestPerformance)

                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4">

                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Média</div>
                        <div class="stat-value text-2xl">
                            {{ $classroom->latestPerformance->average_grade }}
                        </div>
                    </div>

                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Sociabilidade</div>
                        <div class="stat-value text-2xl">
                            {{ $classroom->latestPerformance->sociability }}
                        </div>
                    </div>

                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Autonomia</div>
                        <div class="stat-value text-2xl">
                            {{ $classroom->latestPerformance->autonomy }}
                        </div>
                    </div>

                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Engajamento</div>
                        <div class="stat-value text-2xl">
                            {{ $classroom->latestPerformance->engagement }}
                        </div>
                    </div>

                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Comunicação</div>
                        <div class="stat-value text-2xl">
                            {{ $classroom->latestPerformance->communication }}
                        </div>
                    </div>

                </div>

                <p class="text-xs text-base-content/50 mt-4">
                    Última avaliação:
                    {{ $classroom->latestPerformance->year }}
                    ·
                    {{ $classroom->latestPerformance->period->label() }}
                </p>

            @else

                <div class="alert mt-4">
                    <span>
                        Essa turma ainda não possui uma avaliação de desempenho.
                    </span>
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- BOTÃO SALVAR --}}
    {{-- ========================================================= --}}

    <div class="flex justify-end">

        <button
            type="submit"
            class="btn btn-primary"
        >
            Salvar alterações
        </button>

    </div>

</form>

</x-panel.coordinator>