<x-dynamic-component :component="'panel.' . auth()->user()->role->value">
    <div class="card col-span-1 row-span-1 h-fit min-w-0 bg-base-200 p-3 shadow-xl sm:col-span-2 sm:p-4 lg:col-span-4 lg:row-span-4 lg:p-6 max-[639px]:[&_h1]:text-2xl max-[639px]:[&_h2]:text-lg max-[639px]:[&_p]:text-base max-[639px]:[&_p.uppercase]:text-sm max-[639px]:[&_span]:text-sm">
    
        <div class="grid min-w-0 grid-cols-1 gap-3 sm:gap-4">
    
            {{-- ========================================================= --}}
            {{-- HEADER --}}
            {{-- ========================================================= --}}
    
            <div class="card bg-base-100 shadow-md col-span-1 md:col-span-12">
                <div class="card-body min-w-0 p-4 sm:p-6">
    
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    
                        {{-- Informações do professor --}}
                        <div class="flex min-w-0 flex-col items-center text-center md:flex-row md:text-left">
    
                            {{-- Foto --}}
                            <div class="flex shrink-0 flex-col items-center p-2 md:flex-row md:p-4">
    
                                @if($teacherInfo->teacher->photo ?? false)
    
                                    <div class="
                                        overflow-hidden
                                        rounded-[1vmax]
                                        border
                                        border-base-300
                                        bg-base-200
                                        h-20
                                        w-20
                                        sm:h-24
                                        sm:w-24
                                    ">
                                        <img
                                            src="{{ asset('storage/' . $teacherInfo->teacher->photo) }}"
                                            alt="Foto de {{ $teacherInfo->name }}"
                                            class="w-full h-full object-cover"
                                        >
                                    </div>
    
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
                                        h-20
                                        w-20
                                        sm:h-24
                                        sm:w-24
                                    ">
    
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8"
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
                                    min-w-0 p-2 text-center md:p-4 md:text-left
                                    items-center
                                    gap-x-2
                                    mt-1
                                    text-sm
                                ">
    
                                    <h1 class="
                                        break-words text-2xl sm:text-3xl
                                        font-Sans
                                        font-bold
                                        text-primary-dark
                                    ">
                                        {{ $teacherInfo->name }}
                                    </h1>
    
                                    <div class="flex flex-col items-center gap-y-1 md:flex-row md:flex-wrap md:items-center md:gap-x-2">
    
                                        <span class="text-base-content/60">
                                            Professor
                                        </span>
    
                                        <span class="hidden text-base-content/40 md:inline">
                                            •
                                        </span>
    
                                        <span class="w-full text-base-content/60 md:w-auto">
                                            Formação:
                                            <strong class="text-base-content">
                                                {{ $teacherInfo->teacherSheet?->formation ?? 'Não informada' }}
                                            </strong>
                                        </span>
    
                                        <span class="hidden text-base-content/40 md:inline">
                                            •
                                        </span>
    
                                        <span class="w-full text-base-content/60 md:w-auto">
                                            Registro:
                                            <strong class="text-base-content">
                                                {{ $teacherInfo->teacherSheet?->registration ?? 'Não informado' }}
                                            </strong>
                                        </span>
    
                                    </div>
    
                                </div>
    
                            </div>
    
                        </div>
    
    
                        {{-- Status --}}
                        <div class="
                            flex
                            flex-row
                            items-center
                            justify-between
                            gap-4
                            md:flex-col
                            md:items-end
                        ">
    
                            <span class="
                                badge
                                rounded-full
                                px-3
                                py-2
                                text-sm
                                font-semibold
    
                                {{ match($teacherInfo->status->value) {
                                    'pending' => 'badge-warning',
                                    'active' => 'badge-success',
                                    'inactive' => 'badge-error',
                                    'suspended' => 'badge-neutral',
                                    default => 'badge-ghost',
                                } }}
                            ">
    
                                <span class="mr-1 h-2 w-2 rounded-full bg-current"></span>
    
                                {{ $teacherInfo->status->label() }}
    
                            </span>
    
    
                            <span class="text-sm text-base-content/60">
    
                                Id Nº
    
                                <strong class="text-base-content">
                                    {{ str_pad($teacherInfo->teacherSheet?->id, 4, '0', STR_PAD_LEFT) }}
                                </strong>
    
                            </span>
                            @if(auth()->user()->role->value === 'teacher')
                            <a 
                                href="{{route('teacher.profile.edit')}}"
                                class="btn btn-primary min-h-11 text-base"
                            >
                                Editar
                            </a>
                            @endif
                        </div>
    
                    </div>
    
                </div>
    
            </div>
    
    
            {{-- ========================================================= --}}
            {{-- DADOS PESSOAIS --}}
            {{-- ========================================================= --}}
    
            <div class="
                card
                bg-base-100
                shadow-md
                col-span-1
                md:col-span-7
            ">
    
                <div class="card-body min-w-0 p-4 sm:p-6">
    
                    <h2 class="
                        flex
                        items-center
                        gap-2
                        text-lg
                        font-bold
                        uppercase
                        text-error
                    ">
                        Dados pessoais
                    </h2>
    
    
                    <div class="
                        grid
                        grid-cols-1
                        sm:grid-cols-2
                        gap-x-6
                        gap-y-5
                        mt-5
                    ">
    
                        {{-- Nome --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Nome completo
                            </p>
    
                            <p class="mt-1 text-base font-medium">
                                {{ $teacherInfo->name }}
                            </p>
    
                        </div>
    
    
                        {{-- Nascimento --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Data de nascimento
                            </p>
    
                            <p class="mt-1 text-base font-medium">
                                {{ $teacherInfo->birth_date?->format('d/m/Y') ?? 'Não informado' }}
                            </p>
    
                        </div>
    
    
                        {{-- Sexo --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Sexo
                            </p>
    
                            <p class="mt-1 text-base font-medium">
    
                                {{ match($teacherInfo->gender) {
                                    'M' => 'Masculino',
                                    'F' => 'Feminino',
                                    'O' => 'Outro',
                                    default => 'Não informado',
                                } }}
    
                            </p>
    
                        </div>
    
    
                        {{-- CPF --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                CPF
                            </p>
    
                            <p class="mt-1 text-base font-medium">
                                {{ $teacherInfo->cpf ?? 'Não informado' }}
                            </p>
    
                        </div>
    
    
                        {{-- RG --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                RG
                            </p>
    
                            <p class="mt-1 text-base font-medium">
                                {{ $teacherInfo->rg ?? 'Não informado' }}
                            </p>
    
                        </div>
    
    
                        {{-- Registro --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Matrícula / Registro
                            </p>
    
                            <p class="mt-1 text-base font-medium">
                                {{ $teacherInfo->teacherSheet->registration ?? 'Não informado' }}
                            </p>
    
                        </div>
    
                    </div>
    
                </div>
    
            </div>
    
    
            {{-- ========================================================= --}}
            {{-- CONTA E CONTATO --}}
            {{-- ========================================================= --}}
    
            <div class="
                card
                bg-base-100
                shadow-md
                col-span-1
                md:col-span-5
            ">
    
                <div class="card-body min-w-0 p-4 sm:p-6">
    
                    <h2 class="
                        flex
                        items-center
                        gap-2
                        text-lg
                        font-bold
                        uppercase
                        text-error
                    ">
                        Conta e contato
                    </h2>
    
    
                    <div class="mt-5 space-y-5">
    
                        {{-- Username --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Usuário
                            </p>
    
                            <p class="mt-1 text-base font-medium break-words">
                                {{ $teacherInfo->teacher->username ?? 'Não informado' }}
                            </p>
    
                        </div>
    
    
                        {{-- E-mail --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                E-mail
                            </p>
    
                            <p class="mt-1 break-all text-base font-medium">
                                {{ $teacherInfo->teacher->email ?? 'Não informado' }}
                            </p>
    
                        </div>
    
    
                        {{-- Telefone --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Telefone
                            </p>
    
                            <p class="mt-1 text-base font-medium break-words">
                                {{ $teacherInfo->phone ?? 'Não informado' }}
                            </p>
    
                        </div>
    
                    </div>
    
                </div>
    
            </div>
    
    
            {{-- ========================================================= --}}
            {{-- FORMAÇÃO PROFISSIONAL --}}
            {{-- ========================================================= --}}
    
            <div class="
                card
                bg-base-100
                shadow-md
                col-span-1
                md:col-span-7
            ">
    
                <div class="card-body min-w-0 p-4 sm:p-6">
    
                    <h2 class="
                        flex
                        items-center
                        gap-2
                        text-lg
                        font-bold
                        uppercase
                        text-error
                    ">
                        Formação profissional
                    </h2>
    
    
                    <div class="
                        grid
                        grid-cols-1
                        sm:grid-cols-2
                        gap-x-6
                        gap-y-5
                        mt-5
                    ">
    
                        {{-- Formação --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Formação
                            </p>
    
                            <p class="mt-1 text-base font-medium break-words">
                                {{ $teacherInfo->teacherSheet?->formation ?? 'Não informada' }}
                            </p>
    
                        </div>
    
    
                        {{-- Especialização --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Especialização
                            </p>
    
                            <p class="mt-1 text-base font-medium break-words">
                                {{ $teacherInfo->teacherSheet?->specialization ?? 'Não informada' }}
                            </p>
    
                        </div>
    
    
                        {{-- Matrícula --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Matrícula / Registro
                            </p>
    
                            <p class="mt-1 text-base font-medium">
                                {{ $teacherInfo->teacherSheet?->registration ?? 'Não informado' }}
                            </p>
    
                        </div>
    
    
                        {{-- Contratação --}}
                        <div>
    
                            <p class="text-sm uppercase text-base-content/60">
                                Data de contratação
                            </p>
    
                            <p class="mt-1 text-base font-medium">
                                {{ $teacherInfo->teacherSheet?->hire_date?->format('d/m/Y') ?? 'Não informada' }}
                            </p>
    
                        </div>
    
                    </div>
    
                </div>
    
            </div>
    
    
            {{-- ========================================================= --}}
            {{-- STATUS --}}
            {{-- ========================================================= --}}
    
            <div class="
                card
                bg-base-100
                shadow-md
                col-span-1
                md:col-span-5
            ">
    
                <div class="card-body min-w-0 p-4 sm:p-6">
    
                    <h2 class="
                        flex
                        items-center
                        gap-2
                        text-lg
                        font-bold
                        uppercase
                        text-error
                    ">
                        Status do professor
                    </h2>
    
    
                    <div class="mt-5">
    
                        <p class="text-sm uppercase text-base-content/60">
                            Situação atual
                        </p>
    
                        <div class="mt-3">
    
                            <span class="
                                badge
                                rounded-full
                                px-3
                                py-2
                                text-sm
                                font-semibold
    
                                {{ match($teacherInfo->status->value) {
                                    'pending' => 'badge-warning',
                                    'active' => 'badge-success',
                                    'inactive' => 'badge-error',
                                    'suspended' => 'badge-neutral',
                                    default => 'badge-ghost',
                                } }}
                            ">
    
                                <span class="mr-1 h-2 w-2 rounded-full bg-current"></span>
    
                                {{$teacherInfo->status->label()}}
    
                            </span>
    
                        </div>
    
                    </div>
    
    
                    <div class="
                        grid
                        grid-cols-1
                        sm:grid-cols-2
                        gap-3
                        mt-auto
                        pt-5
                    ">
    
    
    
                    </div>
    
                </div>
    
            </div>
    
    
            {{-- ========================================================= --}}
            {{-- ENDEREÇO --}}
            {{-- ========================================================= --}}
    
            <div class="
                card
                bg-base-100
                shadow-md
                col-span-1
                md:col-span-7
            ">
    
                <div class="card-body min-w-0 p-4 sm:p-6">
    
                    <h2 class="
                        flex
                        items-center
                        gap-2
                        text-lg
                        font-bold
                        uppercase
                        text-error
                    ">
                        Endereço
                    </h2>
    
    
                    <div class="mt-5">
    
                        @if($teacherInfo->street || $teacherInfo->number)
    
                            <p class="text-base font-medium break-words">
                                {{ $teacherInfo->street ?? '' }}
                                @if($teacherInfo->number)
                                    , {{ $teacherInfo->number }}
                                @endif
                            </p>
    
                            <p class="mt-1 text-base break-words">
                                {{ $teacherInfo->district ?? 'Bairro não informado' }}
                            </p>
    
                            <p class="mt-1 text-base text-base-content/60 break-words">
                                {{ $teacherInfo->city ?? 'Cidade não informada' }}
    
                                @if($teacherInfo->state)
                                    — {{ $teacherInfo->state }}
                                @endif
                            </p>
    
                        @else
    
                            <p class="text-base text-base-content/50">
                                Endereço não informado.
                            </p>
    
                        @endif
    
                    </div>
    
                </div>
    
            </div>
    
    
            {{-- ========================================================= --}}
            {{-- HISTÓRICO --}}
            {{-- ========================================================= --}}
    
            <div class="
                card
                bg-base-100
                shadow-md
                col-span-1
                md:col-span-5
            ">
    
                <div class="card-body min-w-0 p-4 sm:p-6">
    
                    <h2 class="
                        flex
                        items-center
                        gap-2
                        text-lg
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
                        ml-2
                        pl-5
                        mt-5
                        space-y-5
                    ">
    
                        {{-- Cadastro --}}
                        <div class="relative">
    
                            <span class="
                                absolute
                                -left-6
                                top-1
                                h-2
                                w-2
                                rounded-full
                                bg-primary
                                ring-4
                                ring-base-100
                            "></span>
    
                            <p class="text-base font-semibold">
                                Professor cadastrado
                            </p>
    
                            <p class="text-sm text-base-content/60">
                                {{ $teacherInfo->created_at?->format('d/m/Y H:i') }}
                            </p>
    
                        </div>
    
    
                        {{-- Atualização --}}
                        <div class="relative">
    
                            <span class="
                                absolute
                                -left-6
                                top-1
                                h-2
                                w-2
                                rounded-full
                                bg-primary
                                ring-4
                                ring-base-100
                            "></span>
    
                            <p class="text-base font-semibold">
                                Última atualização
                            </p>
    
                            <p class="text-sm text-base-content/60">
                                {{ $teacherInfo->updated_at?->format('d/m/Y H:i') }}
                            </p>
    
                        </div>
    
                    </div>
    
                </div>
    
            </div>
    
    
            {{-- ========================================================= --}}
            {{-- INFORMAÇÕES ADICIONAIS --}}
            {{-- ========================================================= --}}
    
            @if($teacherInfo->notes)
    
                <div class="
                    card
                    bg-base-100
                    shadow-md
                    col-span-1
                    md:col-span-12
                ">
    
                    <div class="card-body min-w-0 p-4 sm:p-6">
    
                        <h2 class="
                            text-lg
                            font-bold
                            uppercase
                            text-error
                        ">
                            Informações adicionais
                        </h2>
    
    
                        <p class="
                            text-base
                            text-base-content/70
                            whitespace-pre-line
                            mt-4
                        ">
                            {{ $teacherInfo->notes }}
                        </p>
    
                    </div>
    
                </div>
    
            @endif
    
        </div>
    
    </div>
</x-dynamic-component>
