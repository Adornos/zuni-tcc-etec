<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    @php
        $canAddStudents = Auth::user()->isCoordinator();
    @endphp

    <div class="container mx-auto col-span-4 row-span-4">

        @if ($canAddStudents)
            <form
                method="POST"
                action="{{ route('coordinator.classroom.students.update', $classroom) }}"
            >
                @csrf
                @method('PUT')
        @endif

            {{-- CAIXA ÚNICA --}}
            <div class="card bg-base-100 shadow-md">
                <div class="card-body p-6">

                    {{-- CABEÇALHO --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 mb-2 border-b border-base-200">

                        <div>
                            <h2 class="text-lg font-bold text-Cprimary">
                                Alunos da turma
                            </h2>
                            <p class="text-sm text-base-content/60 mt-1">
                                {{ $classroom->name }}
                            </p>
                        </div>

                        @if ($canAddStudents)
                            <a
                                href="{{ route('coordinator.classroom.show', $classroom->id) }}"
                                class="btn btn-sm bg-Cprimary hover:bg-Cprimary/90 text-white border-none w-full sm:w-auto"
                            >
                                ← Voltar
                            </a>
                        @endif

                    </div>

                    @if ($canAddStudents)

                        {{-- ALUNOS DA TURMA --}}
                        <h3 class="text-sm font-semibold text-Cprimary mt-4 mb-1 px-3">
                            Na turma
                        </h3>

                        <div class="flex flex-col divide-y divide-base-200">

                            @forelse ($classroom->students as $studentSheet)

                                <label
                                    for="student-{{ $studentSheet->user->id }}"
                                    class="flex items-center gap-4 px-3 py-3 cursor-pointer transition-colors duration-200 hover:bg-base-200/60 bg-base-200/60"
                                >

                                    <div class="avatar shrink-0">
                                        <div class="w-12 h-12 rounded-full">
                                            <img
                                                src="https://ui-avatars.com/api/?name={{ urlencode($studentSheet->user->name ?? 'Sem nome') }}&background=random"
                                                alt="Avatar de {{ $studentSheet->user->name }}"
                                            >
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-base text-Cprimary leading-tight truncate">
                                            {{ $studentSheet->user->name }}
                                        </h3>
                                        <p class="text-xs text-base-content/60 mt-1 truncate">
                                            Matrícula: {{ $studentSheet->registration_number ?? '—' }}
                                        </p>
                                    </div>

                                    <input
                                        class="checkbox checkbox-sm border-Cprimary [--chkbg:theme(colors.Cprimary)] [--chkfg:white] shrink-0"
                                        type="checkbox"
                                        name="students[]"
                                        value="{{ $studentSheet->user->id }}"
                                        id="student-{{ $studentSheet->user->id }}"
                                        checked
                                    >

                                </label>

                            @empty

                                <div class="py-3">
                                    <div class="alert">
                                        <span>Nenhum aluno está associado a esta turma.</span>
                                    </div>
                                </div>

                            @endforelse

                        </div>

                        {{-- ALUNOS DISPONÍVEIS --}}
                        <h3 class="text-sm font-semibold text-Cprimary mt-8 mb-1 px-3">
                            Disponíveis
                        </h3>

                        <div class="flex flex-col divide-y divide-base-200">

                            @forelse ($availableStudents as $studentSheet)

                                <label
                                    for="student-{{ $studentSheet->user->id }}"
                                    class="flex items-center gap-4 px-3 py-3 cursor-pointer transition-colors duration-200 hover:bg-base-200/60"
                                >

                                    <div class="avatar shrink-0">
                                        <div class="w-12 h-12 rounded-full">
                                            <img
                                                src="https://ui-avatars.com/api/?name={{ urlencode($studentSheet->user->name ?? 'Sem nome') }}&background=random"
                                                alt="Avatar de {{ $studentSheet->user->name }}"
                                            >
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-base text-Cprimary leading-tight truncate">
                                            {{ $studentSheet->user->name }}
                                        </h3>
                                        <p class="text-xs text-base-content/60 mt-1 truncate">
                                            Matrícula: {{ $studentSheet->registration_number ?? '—' }}
                                        </p>
                                    </div>

                                    <input
                                        class="checkbox checkbox-sm border-Cprimary [--chkbg:theme(colors.Cprimary)] [--chkfg:white] shrink-0"
                                        type="checkbox"
                                        name="students[]"
                                        value="{{ $studentSheet->user->id }}"
                                        id="student-{{ $studentSheet->user->id }}"
                                    >

                                </label>

                            @empty

                                <div class="py-3">
                                    <div class="alert">
                                        <span>Não há alunos sem turma no momento.</span>
                                    </div>
                                </div>

                            @endforelse

                        </div>

                        {{-- AÇÕES --}}
                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6 pt-5 border-t border-base-200">

                            <a
                                href="{{ route('coordinator.classroom.show', $classroom->id) }}"
                                class="btn btn-outline border-Cprimary text-Cprimary hover:bg-Cprimary hover:text-white w-full sm:w-auto"
                            >
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="btn bg-Cprimary hover:bg-Cprimary/90 text-white border-none w-full sm:w-auto"
                            >
                                Salvar alunos
                            </button>

                        </div>

                    @else

                        {{-- VISÃO DO PROFESSOR --}}
                        <div class="flex flex-col divide-y divide-base-200">

                            @forelse ($classroom->students as $studentSheet)

                                <a
                                    href="{{ route('teacher.student.show', ['student' => $studentSheet->user->id]) }}"
                                    class="flex items-center gap-4 px-3 py-3 cursor-pointer transition-colors duration-200 hover:bg-base-200/60"
                                >

                                    <div class="avatar shrink-0">
                                        <div class="w-12 h-12 rounded-full">
                                            <img
                                                src="https://ui-avatars.com/api/?name={{ urlencode($studentSheet->user->name ?? 'Sem nome') }}&background=random"
                                                alt="Avatar de {{ $studentSheet->user->name }}"
                                            >
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-base text-Cprimary leading-tight truncate">
                                            {{ $studentSheet->user->name }}
                                        </h3>
                                        <p class="text-xs text-base-content/60 mt-1 truncate">
                                            Matrícula: {{ $studentSheet->registration_number ?? '—' }}
                                        </p>
                                    </div>

                                    <span class="text-Cprimary shrink-0">→</span>

                                </a>

                            @empty

                                <div class="py-3">
                                    <div class="alert">
                                        <span>Nenhum aluno está associado a esta turma.</span>
                                    </div>
                                </div>

                            @endforelse

                        </div>

                    @endif

                </div>
            </div>

        @if ($canAddStudents)
            </form>
        @endif

    </div>

</x-dynamic-component>