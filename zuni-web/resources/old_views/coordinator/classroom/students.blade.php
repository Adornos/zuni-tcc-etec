<x-panel.coordinator>

    <div class="col-span-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">
                            Alunos da turma
                        </h2>

                        <p class="text-base-content/60">
                            {{ $classroom->name }}
                        </p>
                    </div>

                    <div class="badge badge-primary badge-lg">
                        {{ $classroom->students->count() }} alunos
                    </div>
                </div>

                <form
                    action="{{ route('coordinator.classroom.students.update', $classroom) }}"
                    method="POST"
                >
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">

                        {{-- ALUNOS JÁ ASSOCIADOS À TURMA --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <h3 class="text-lg font-semibold">
                                    Alunos da turma
                                </h3>

                                <span class="badge badge-success">
                                    {{ $classroom->students->count() }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">

                                @forelse ($classroom->students as $studentSheet)

                                    <label
                                        class="flex items-center gap-3 p-4 rounded-box
                                               border border-base-300
                                               hover:bg-base-200
                                               cursor-pointer transition"
                                    >

                                        <input
                                            type="checkbox"
                                            name="students[]"
                                            value="{{ $studentSheet->user->id }}"
                                            class="checkbox checkbox-primary"
                                            checked
                                        >

                                        <div class="flex-1 min-w-0">

                                            <p class="font-semibold truncate">
                                                {{ $studentSheet->user->name }}
                                            </p>

                                            <p class="text-sm text-base-content/60">
                                                Matrícula:
                                                {{ $studentSheet->registration_number ?? '—' }}
                                            </p>

                                        </div>

                                    </label>

                                @empty

                                    <div class="col-span-full">
                                        <div class="alert">
                                            <span>
                                                Nenhum aluno está associado a esta turma.
                                            </span>
                                        </div>
                                    </div>

                                @endforelse

                            </div>
                        </div>


                        {{-- ALUNOS SEM TURMA --}}
                        <div>

                            <div class="flex items-center gap-2 mb-3">
                                <h3 class="text-lg font-semibold">
                                    Alunos disponíveis
                                </h3>

                                <span class="badge badge-warning">
                                    {{ $availableStudents->count() }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">

                                @forelse ($availableStudents as $studentSheet)

                                    <label
                                        class="flex items-center gap-3 p-4 rounded-box
                                               border border-base-300
                                               hover:bg-base-200
                                               cursor-pointer transition"
                                    >

                                        <input
                                            type="checkbox"
                                            name="students[]"
                                            value="{{ $studentSheet->user->id }}"
                                            class="checkbox checkbox-primary"
                                        >

                                        <div class="flex-1 min-w-0">

                                            <p class="font-semibold truncate">
                                                {{ $studentSheet->user->name }}
                                            </p>

                                            <p class="text-sm text-base-content/60">
                                                Matrícula:
                                                {{ $studentSheet->registration_number ?? '—' }}
                                            </p>

                                        </div>

                                    </label>

                                @empty

                                    <div class="col-span-full">
                                        <div class="alert alert-success">
                                            <span>
                                                Não há alunos sem turma no momento.
                                            </span>
                                        </div>
                                    </div>

                                @endforelse

                            </div>
                        </div>


                        {{-- BOTÃO --}}
                        <div class="flex justify-end pt-4 border-t border-base-300">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Salvar alunos
                            </button>

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

</x-panel.coordinator>