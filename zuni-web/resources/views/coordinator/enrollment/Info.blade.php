<form class="card bg-base-200 h-fit shadow-xl p-6 col-span-4">

    <div class="grid grid-cols-1 gap-[1vmax] ">


        {{-- HEADER --}}
        <div class="card bg-base-100 shadow-md -span-1 md:col-span-12">

            <div class="card-body p-[1.5vmax]">

                <div class="flex flex-row gap-[1.5vmax] md:flex-row md:items-center md:justify-between">


                    {{-- Informações do aluno --}}
                    <div class="flex-col items-center">



                        {{-- foto --}}
                        <div class="min-w-0 p-[1vmax] flex flex-row">
                            @if($enrollmentInfo->student->photo ?? false)

                            <div class="
                                overflow-hidden
                                rounded-[1vmax]
                                border
                                border-base-300
                                bg-base-200
                                w-[10vmax]
                                h-[10vmax]">
                                <img
                                    src="{{ asset('storage/' . $enrollmentInfo->student->photo) }}"
                                    alt="Foto de {{ $enrollmentInfo->name }}"
                                    class="w-full h-full object-cover">
                        </div>

                            {{-- Caso não tenha foto --}}
                        @else

                            <div class="
                                flex
                                items-center
                                justify-center
                                rounded-[1vmax]
                                border-2
                                border-dashed
                                border-base-300
                                bg-base-200
                                text-base-content/40
                                w-[10vmax]
                                h-[10vmax]
                            ">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-[3vmax] h-[3vmax]"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"
                                    />
                                </svg>

                            </div>

                        @endif
                            

                        <div class="
                            p-[2vmax]
                            items-center
                            gap-x-[.5vmax]
                            mt-[.3vmax]
                            text-[.9vmax]
                        ">
                            <h1 class="
                                text-[2vmax]
                                font-Sans
                                font-bold
                                text-primary-dark
                            ">
                                {{ $enrollmentInfo->name }}
                            </h1>



                                <span class="font-semibold">
                                    1º Ano
                                </span>

                                <span class="text-base-content/40">
                                    •
                                </span>

                                <span class="text-base-content/60">
                                    Turma {{ $enrollmentInfo->class }}
                                </span>

                                <span class="text-base-content/40">
                                    •
                                </span>

                                <span class="text-base-content/60">
                                    Nascimento:
                                    <strong class="text-base-content">
                                        {{ \Carbon\Carbon::parse($enrollmentInfo->birth_date)->format('d/m/Y') }}
                                    </strong>
                                </span>

                                <span class="text-base-content/40">
                                    •
                                </span>

                                <span class="text-base-content/60">
                                    Sexo:
                                    <strong class="text-base-content">
                                        {{ $enrollmentInfo->gender }}
                                    </strong>
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="
                        flex
                        flex-row
                        items-center
                        justify-between
                        gap-[1.5vmax]
                        md:flex-col
                        md:items-end
                    ">

                        <span class="
                            badge
                            badge-warning
                            gap-[.3vmax]
                            rounded-full
                            px-[1vmax]
                            py-[1vmax]
                            text-[.8vmax]
                            font-semibold
                        ">
                            <span class="w-[.35vmax] h-[.35vmax] rounded-full bg-current"></span>

                            {{ $enrollmentInfo->status }}
                        </span>


                        <span class="text-[.85vmax] text-base-content/60">

                            Matrícula Nº

                            <strong class="text-base-content">
                                {{ str_pad($enrollmentInfo->id, 4, '0', STR_PAD_LEFT) }}
                            </strong>

                        </span>

                    </div>

                </div>

            </div>

        </div>



        {{-- DADOS DO ALUNO --}}
        <div class="
            card
            bg-base-100
            shadow-md
            col-span-1
            md:col-span-7
        ">

            <div class="card-body p-[1.5vmax]">

                <h2 class="
                    flex
                    items-center
                    gap-[.5vmax]
                    text-[1.1vmax]
                    font-bold
                    uppercase
                    text-error
                ">
                    Dados do aluno

                </h2>


                <div class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    gap-x-[3vmax]
                    gap-y-[1.5vmax]
                    mt-[1.5vmax]
                ">


                    {{-- Nome --}}
                    <div>

                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Nome completo
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $enrollmentInfo->name }}
                        </p>

                    </div>


                    {{-- Nascimento --}}
                    <div>

                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Data de nascimento
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ \Carbon\Carbon::parse($enrollmentInfo->birth_date)->format('d/m/Y') }}
                        </p>

                    </div>


                    {{-- Sexo --}}
                    <div>

                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Sexo
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $enrollmentInfo->gender }}
                        </p>

                    </div>


                    {{-- Ano --}}
                    <div>

                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Ano letivo
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            1º Ano
                        </p>

                    </div>


                    {{-- Turma --}}
                    <div>

                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Turma
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $enrollmentInfo->class }}
                        </p>

                    </div>


                    {{-- Matrícula --}}
                    <div>

                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Nº da matrícula
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ str_pad($enrollmentInfo->id, 4, '0', STR_PAD_LEFT) }}
                        </p>

                    </div>

                </div>

            </div>

        </div>



        {{-- STATUS DA MATRÍCULA --}}
        <div class="
            card
            bg-base-100
            shadow-md
            col-span-1
            md:col-span-5
        ">

            <div class="card-body p-[1.5vmax]">

                <h2 class="
                    flex
                    items-center
                    gap-[.5vmax]
                    text-[1.1vmax]
                    font-bold
                    uppercase
                    text-error
                ">
                    Status da matrícula
                </h2>


                <div class="mt-[1.5vmax]">

                    <p class="text-[.7vmax] uppercase text-base-content/60">
                        Situação atual
                    </p>


                    <div class="mt-[.7vmax]">

                        <span class="
                            badge
                            badge-warning
                            rounded-full
                            px-[1vmax]
                            py-[1vmax]
                            text-[.8vmax]
                            font-semibold
                        ">

                            <span class="mr-[.3vmax] w-[.35vmax] h-[.35vmax] rounded-full bg-current"></span>

                            {{ $enrollmentInfo->status }}

                        </span>

                    </div>

                </div>


                <div class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    gap-[.7vmax]
                    mt-auto
                    pt-[1.5vmax]
                ">

                    <button class="
                        btn
                        btn-primary
                        w-full
                        min-h-[2.5vmax]
                        h-[2.5vmax]
                        text-[.8vmax]
                    ">
                        Aprovar matrícula
                    </button>


                    <button class="
                        btn
                        btn-outline
                        w-full
                        min-h-[2.5vmax]
                        h-[2.5vmax]
                        text-[.8vmax]
                    ">
                        Editar
                    </button>

                </div>

            </div>

        </div>



        {{-- ENDEREÇO --}}
        <div class="
            card
            bg-base-100
            shadow-md
            col-span-1
            md:col-span-7
        ">

            <div class="card-body p-[1.5vmax]">

                <h2 class="
                    flex
                    items-center
                    gap-[.5vmax]
                    text-[1.1vmax]
                    font-bold
                    uppercase
                    text-error
                ">
                    Endereço
                </h2>


                <div class="mt-[1.5vmax]">

                    <p class="text-[1vmax] font-medium">
                        {{ $enrollmentInfo->street }},
                        {{ $enrollmentInfo->number }}
                    </p>

                    <p class="text-[1vmax] mt-[.2vmax]">
                        {{ $enrollmentInfo->district }}
                    </p>

                    <p class="text-[.9vmax] text-base-content/60 mt-[.2vmax]">
                        {{ $enrollmentInfo->city }} —
                        {{ $enrollmentInfo->state }}
                    </p>

                </div>

            </div>

        </div>



        {{-- HISTÓRICO --}}
        <div class="
            card
            bg-base-100
            shadow-md
            col-span-1
            md:col-span-5
        ">

            <div class="card-body p-[1.5vmax]">

                <h2 class="
                    flex
                    items-center
                    gap-[.5vmax]
                    text-[1.1vmax]
                    font-bold
                    uppercase
                    text-error
                ">
                    Histórico
                </h2>


                <div class="
                    relative
                    border-l
                    border-base-300
                    ml-[.5vmax]
                    pl-[1.2vmax]
                    mt-[1.5vmax]
                    space-y-[1.5vmax]
                ">

                    <div class="relative">

                        <span class="
                            absolute
                            -left-[1.5vmax]
                            top-[.3vmax]
                            w-[.6vmax]
                            h-[.6vmax]
                            rounded-full
                            bg-primary
                            ring-[.3vmax]
                            ring-base-100
                        "></span>


                        <p class="text-[.9vmax] font-semibold">
                            Matrícula criada
                        </p>

                        <p class="text-[.75vmax] text-base-content/60">
                            {{ $enrollmentInfo->created_at }}
                        </p>

                    </div>


                    <div class="relative">

                        <span class="
                            absolute
                            -left-[1.5vmax]
                            top-[.3vmax]
                            w-[.6vmax]
                            h-[.6vmax]
                            rounded-full
                            bg-primary
                            ring-[.3vmax]
                            ring-base-100
                        "></span>


                        <p class="text-[.9vmax] font-semibold">
                            Última atualização
                        </p>

                        <p class="text-[.75vmax] text-base-content/60">
                            {{ $enrollmentInfo->updated_at }}
                        </p>

                    </div>

                </div>

            </div>

        </div>



        {{-- RESPONSÁVEL --}}
        <div class="
            card
            bg-base-100
            shadow-md
            col-span-1
            md:col-span-7
        ">

            <div class="card-body p-[1.5vmax]">

                <h2 class="
                    flex
                    items-center
                    gap-[.5vmax]
                    text-[1.1vmax]
                    font-bold
                    uppercase
                    text-error
                ">
                    ♙
                    Responsável
                </h2>


                <div class="
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-[1vmax]
                    bg-base-200
                    border
                    border-base-300
                    rounded-[.8vmax]
                    p-[1vmax]
                    mt-[1vmax]
                ">


                    <div class="flex items-center gap-[1vmax] min-w-0">

                        <div class="min-w-0">

                            <p class="text-[.9vmax] font-semibold">
                                {{ $enrollmentInfo->guardian->name ?? 'Default Guardian' }}
                            </p>

                            <p class="text-[.7vmax] text-base-content/60">
                                Responsável legal · sem e-mail cadastrado
                            </p>

                        </div>

                    </div>


                    <button class="
                        btn
                        btn-sm
                        bg-warning/20
                        border-0
                        text-warning
                        hover:bg-warning/30
                        text-[.7vmax]
                    ">
                        Verificar
                    </button>

                </div>

            </div>

        </div>



        {{-- INFORMAÇÕES ADICIONAIS --}}
        <div class="
            card
            bg-base-100
            shadow-md
            col-span-1
            md:col-span-5
        ">

            <div class="card-body p-[1.5vmax]">

                <h2 class="
                    text-[1.1vmax]
                    font-bold
                    uppercase
                    text-error
                ">
                    Informações adicionais
                </h2>


                <div class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    gap-[1vmax]
                    mt-[1.5vmax]
                ">

                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Neurodivergente
                        </p>

                        <p class="text-[.9vmax] mt-[.2vmax]">
                            {{ $enrollmentInfo->neurodivergent ?: 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Alergia
                        </p>

                        <p class="text-[.9vmax] mt-[.2vmax]">
                            {{ $enrollmentInfo->allergy ?: 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Restrição alimentar
                        </p>

                        <p class="text-[.9vmax] mt-[.2vmax]">
                            {{ $enrollmentInfo->food_restriction ?: 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Cuidados especiais
                        </p>

                        <p class="text-[.9vmax] mt-[.2vmax]">
                            {{ $enrollmentInfo->special_care ?: 'Não informado' }}
                        </p>
                    </div>

                </div>

            </div>

        </div>



        {{-- OBSERVAÇÕES --}}
        @if($enrollmentInfo->notes)

            <div class="
                card
                bg-base-100
                shadow-md
                col-span-1
                md:col-span-12
            ">

                <div class="card-body p-[1.5vmax]">

                    <h2 class="
                        text-[1.1vmax]
                        font-bold
                        uppercase
                        text-error
                    ">
                        Observações
                    </h2>


                    <p class="
                        text-[.9vmax]
                        text-base-content/70
                        whitespace-pre-line
                        mt-[1vmax]
                    ">
                        {{ $enrollmentInfo->notes }}
                    </p>

                </div>

            </div>

        @endif

    </div>

</form>