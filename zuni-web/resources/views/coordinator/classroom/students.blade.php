<x-panel.coordinator>

    <div class="container mx-auto col-span-4 row-span-4">

        {{-- CABEÇALHO --}}
        <div class=" flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            <div>
                <h1 class="
                    text-2xl
                    sm:text-3xl
                    font-bold
                    text-base-content
                ">
                    Alunos da sala
                </h1>

                <p class="
                    text-sm
                    sm:text-base
                    text-base-content/60
                    mt-1
                ">
                    Sala:
                    <strong class="text-base-content">
                        {{ $classroom->name }}
                    </strong>
                </p>
            </div>

            <a
                href="{{ route('coordinator.classroom.show', $classroom->id) }}"
                class="btn btn-outline btn-sm sm:btn-md w-full sm:w-auto"
            >
                ← Voltar
            </a>

        </div>


        {{-- MENSAGEM DE SUCESSO --}}
        @if (session('success'))

            <div class="alert alert-success mb-6">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- ERROS --}}
        @if ($errors->any())

            <div class="alert alert-error mb-6">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"
                    />
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


        {{-- FORMULÁRIO --}}
        <form
            method="POST"
            action="{{ route('coordinator.classroom.students.update', $classroom) }}"
        >
            @csrf
            @method('PUT')


            <div class="
                card
                bg-base-100
                border
                border-base-200
                shadow-sm
            ">

                {{-- CARD HEADER --}}
                <div class="card-body p-4 sm:p-5 md:p-6">

                    <div class="
                        flex
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-2
                        mb-5
                    ">

                        <div>
                            <h2 class="
                                card-title
                                text-lg
                                sm:text-xl
                            ">
                                Selecione os alunos
                            </h2>

                            <p class="
                                text-sm
                                text-base-content/60
                                mt-1
                            ">
                                Escolha quais alunos  terão acesso a esta sala.
                            </p>
                        </div>

                        @if ($students->count())
                            <span class="badge badge-ghost">
                                {{ $students->count() }}
                                {{ $students->count() === 1 ? 'aluno' : 'alunos ' }}
                            </span>
                        @endif

                    </div>


                    {{-- ALUNOS --}}
                    <div class="
                        grid
                        grid-cols-1
                        sm:grid-cols-2
                        lg:grid-cols-3
                        xl:grid-cols-5
                        gap-4
                        sm:gap-5
                        lg:gap-6
                    ">

                        @forelse ($students as $student)

                            @php
                                $isSelected = in_array(
                                    $student->id,
                                    old(
                                        'students',
                                        $classroom->students->pluck('id')->toArray()
                                    )
                                );
                            @endphp

                            <label
                                for="teacher-{{ $student->id }}"
                                class="
                                    card
                                    bg-base-100
                                    border
                                    {{ $isSelected ? 'border-Cprimary ring-2 ring-Cprimary/20' : 'border-base-200' }}
                                    shadow-sm
                                    hover:shadow-md
                                    transition-all
                                    duration-200
                                    cursor-pointer
                                    h-full
                                "
                            >

                                <div class="card-body p-4 sm:p-5">

                                    {{-- TOPO --}}
                                    <div class="
                                        flex
                                        items-start
                                        justify-between
                                        gap-3
                                    ">

                                        {{-- AVATAR --}}
                                        <div class="avatar shrink-0">

                                            <div class="
                                                w-14
                                                h-14
                                                sm:w-16
                                                sm:h-16
                                                rounded-full
                                            ">

                                                <img
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($student->name ?? 'Sem nome') }}&background=random"
                                                    alt="Avatar de {{ $student->name }}"
                                                >

                                            </div>

                                        </div>

                                        {{-- CHECKBOX --}}
                                        <input
                                            class="
                                                checkbox
                                                checkbox-primary
                                                checkbox-sm
                                                sm:checkbox-md
                                            "
                                            type="checkbox"
                                            name="teachers[]"
                                            value="{{ $student->id }}"
                                            id="teacher-{{ $student->id }}"
                                            @checked($isSelected)
                                        >

                                    </div>


                                    {{-- INFORMAÇÕES --}}
                                    <div class="mt-4">

                                        <h3 class="
                                            font-bold
                                            text-base
                                            sm:text-lg
                                            leading-tight
                                        ">
                                            {{ $student->name }}
                                        </h3>

                                        @if ($student->email)

                                            <p class="
                                                text-xs
                                                sm:text-sm
                                                text-base-content/60
                                                mt-1
                                                truncate
                                            ">
                                                {{ $student->email }}
                                            </p>

                                        @endif

                                    </div>


                                    {{-- STATUS --}}
                                    <div class="card-actions mt-4">

                                        @if ($isSelected)

                                            <span class="badge badge-success badge-sm text-white">
                                                ✓ Selecionado
                                            </span>

                                        @else

                                            <span class="badge badge-ghost badge-sm">
                                                Disponível
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </label>

                        @empty

                            <div class="col-span-full">

                                <div class="alert alert-warning">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"
                                        />
                                    </svg>

                                    <span>
                                        Nenhum aluno disponível para atribuição.
                                    </span>

                                </div>

                            </div>

                        @endforelse

                    </div>

                    {{-- PAGINAÇÃO --}}
                    @if ($students->hasPages())

                        <div class="flex flex-col justify-evenly mt-6">

                            {{ $students->links() }}

                        </div>

                    @endif



                    {{-- AÇÕES --}}
                    @if ($students->count())

                        <div class="
                            flex
                            flex-col-reverse
                            sm:flex-row
                            sm:justify-end
                            gap-3
                            mt-6
                            pt-5
                            border-t
                            border-base-200
                        ">

                            <a
                                href="{{ route('coordinator.classroom.show', $classroom->id) }}"
                                class="
                                    btn
                                    btn-ghost
                                    w-full
                                    sm:w-auto
                                "
                            >
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="
                                    btn
                                    bg-Cprimary
                                    hover:bg-Cprimary/90
                                    text-white
                                    border-none
                                    w-full
                                    sm:w-auto
                                "
                            >
                                Salvar alunos 
                            </button>

                        </div>

                    @endif

                </div>

            </div>

        </form>

    </div>

</x-panel.coordinator>
