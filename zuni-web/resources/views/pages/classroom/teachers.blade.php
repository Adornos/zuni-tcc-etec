<x-panel.coordinator>

    <div class="container mx-auto col-span-4 row-span-4">

        {{-- MENSAGEM DE SUCESSO --}}
        @if (session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ERROS --}}
        @if ($errors->any())
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />
                </svg>
                <div>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('coordinator.classroom.teachers.update', $classroom) }}"
        >
            @csrf
            @method('PUT')

            {{-- CAIXA ÚNICA --}}
            <div class="card bg-base-100 shadow-md">
                <div class="card-body p-6">

                    {{-- CABEÇALHO --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 mb-5 border-b border-base-300">

                        <div class="flex flex-col">
                            <h2 class="text-lg font-bold text-Cprimary">
                                Professores da sala
                            </h2>
                            <p class="text-sm text-base-content/60 mt-1">
                                {{ $classroom->name }}
                            </p>
                        </div>

                        <a
                            href="{{ route('coordinator.classroom.show', $classroom->id) }}"
                            class="btn btn-sm bg-Cprimary hover:bg-Cprimary/90 text-white border-none w-full sm:w-auto"
                        >
                            ← Voltar
                        </a>

                    </div>

                    {{-- LISTA DE PROFESSORES --}}
                    <div class="flex flex-col divide-y divide-base-200">

                        @forelse ($teachers as $teacher)

                            @php
                                $isSelected = in_array(
                                    $teacher->id,
                                    old('teachers', $classroom->teachers->pluck('id')->toArray())
                                );
                            @endphp

                            <label
                                for="teacher-{{ $teacher->id }}"
                                class="
                                    flex
                                    items-center
                                    gap-4
                                    px-3
                                    py-3
                                    cursor-pointer
                                    transition-colors
                                    duration-200
                                    hover:bg-base-200/60
                                    {{ $isSelected ? 'bg-base-200/60' : '' }}
                                "
                            >

                                <div class="avatar shrink-0">
                                    <div class="w-12 h-12 rounded-full">
                                        <img
                                            src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name ?? 'Sem nome') }}&background=random"
                                            alt="Avatar de {{ $teacher->name }}"
                                        >
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-base text-Cprimary leading-tight truncate">
                                        {{ $teacher->name }}
                                    </h3>

                                    @if ($teacher->email)
                                        <p class="text-xs text-base-content/60 mt-1 truncate">
                                            {{ $teacher->email }}
                                        </p>
                                    @endif
                                </div>

                                <input
                                    class="checkbox checkbox-sm border-Cprimary [--chkbg:theme(colors.Cprimary)] [--chkfg:white] shrink-0"
                                    type="checkbox"
                                    name="teachers[]"
                                    value="{{ $teacher->id }}"
                                    id="teacher-{{ $teacher->id }}"
                                    @checked($isSelected)
                                >

                            </label>

                        @empty

                            <div class="py-3">
                                <div class="alert alert-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />
                                    </svg>
                                    <span>Nenhum professor disponível para atribuição.</span>
                                </div>
                            </div>

                        @endforelse

                    </div>

                    {{-- PAGINAÇÃO --}}
                    @if ($teachers->hasPages())
                        <div class="mt-6">
                            {{ $teachers->links('pagination.clean') }}
                        </div>
                    @endif

                    {{-- AÇÕES --}}
                    @if ($teachers->count())
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
                                Salvar professores
                            </button>

                        </div>
                    @endif

                </div>
            </div>

        </form>

    </div>

</x-panel.coordinator>