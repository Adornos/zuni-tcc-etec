<x-panel.director>


<div class="card bg-base-200 h-fit shadow-xl p-6 col-span-4">

    <div class="grid grid-cols-1 gap-[1vmax]">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="card bg-base-100 shadow-md col-span-1 md:col-span-12">

            <div class="card-body p-[1.5vmax]">

                <div class="flex flex-col gap-[1.5vmax] md:flex-row md:items-center md:justify-between">

                    {{-- Informações do funcionário --}}
                    <div class="flex items-center">

                        {{-- Foto --}}
                        <div class="p-[1vmax] flex flex-row">

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

                            <div class="p-[2vmax] items-center gap-x-[.5vmax] mt-[.3vmax] text-[.9vmax]">

                                <h1 class="
                                    text-[2vmax]
                                    font-Sans
                                    font-bold
                                    text-primary-dark
                                ">
                                    {{ $employee->name }}
                                </h1>

                                <div class="flex flex-wrap items-center gap-x-[.5vmax] gap-y-[.2vmax]">

                                    <span class="text-base-content/60">
                                        {{ $employee->role->label() }}
                                    </span>

                                    <span class="text-base-content/40">
                                        •
                                    </span>

                                    <span class="text-base-content/60">
                                        Formação:
                                        <strong class="text-base-content">
                                            {{ $employee->roleSheet?->formation ?? 'Não informada' }}
                                        </strong>
                                    </span>

                                    <span class="text-base-content/40">
                                        •
                                    </span>

                                    <span class="text-base-content/60">
                                        Registro:
                                        <strong class="text-base-content">
                                            {{ $employee->roleSheet?->registration ?? 'Não informado' }}
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
                        gap-[1.5vmax]
                        md:flex-col
                        md:items-end
                    ">

                        <span class="
                            badge
                            rounded-full
                            px-[1vmax]
                            py-[1vmax]
                            text-[.8vmax]
                            font-semibold

                            {{ match($employee->status->value) {
                                'pending' => 'badge-warning',
                                'active' => 'badge-success',
                                'inactive' => 'badge-error',
                                'suspended' => 'badge-neutral',
                                default => 'badge-ghost',
                            } }}
                        ">

                            <span class="mr-[.3vmax] w-[.35vmax] h-[.35vmax] rounded-full bg-current"></span>

                            {{ $employee->status->label() }}

                        </span>


                        <span class="text-[.85vmax] text-base-content/60">

                            Registro Nº

                            <strong class="text-base-content">
                                {{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
                            </strong>

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- DADOS PESSOAIS --}}
        {{-- ========================================================= --}}

        <div class="card bg-base-100 shadow-md col-span-1 md:col-span-7">

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
                    Dados pessoais
                </h2>


                <div class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    gap-x-[3vmax]
                    gap-y-[1.5vmax]
                    mt-[1.5vmax]
                ">

                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Nome completo
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->name }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Data de nascimento
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->birth_date?->format('d/m/Y') ?? 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Sexo
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ match($employee->gender) {
                                'M' => 'Masculino',
                                'F' => 'Feminino',
                                'O' => 'Outro',
                                default => 'Não informado',
                            } }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            CPF
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->cpf ?? 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            RG
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->rg ?? 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Matrícula / Registro
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->roleSheet?->registration ?? 'Não informado' }}
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- CONTA E CONTATO --}}
        {{-- ========================================================= --}}

        <div class="card bg-base-100 shadow-md col-span-1 md:col-span-5">

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
                    Conta e contato
                </h2>


                <div class="space-y-[1.2vmax] mt-[1.5vmax]">

                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Usuário
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->username ?? 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            E-mail
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax] break-all">
                            {{ $employee->email ?? 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Telefone
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->phone ?? 'Não informado' }}
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FORMAÇÃO PROFISSIONAL --}}
        {{-- ========================================================= --}}

        <div class="card bg-base-100 shadow-md col-span-1 md:col-span-7">

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
                    Formação profissional
                </h2>


                <div class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    gap-x-[3vmax]
                    gap-y-[1.5vmax]
                    mt-[1.5vmax]
                ">

                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Formação
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->roleSheet?->formation ?? 'Não informada' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Especialização
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->roleSheet?->specialization ?? 'Não informada' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Matrícula / Registro
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->roleSheet?->registration ?? 'Não informado' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-[.7vmax] uppercase text-base-content/60">
                            Data de contratação
                        </p>

                        <p class="text-[1vmax] font-medium mt-[.2vmax]">
                            {{ $employee->roleSheet?->hire_date?->format('d/m/Y') ?? 'Não informada' }}
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- STATUS --}}
        {{-- ========================================================= --}}

        <div class="card bg-base-100 shadow-md col-span-1 md:col-span-5">

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
                    Status do funcionário
                </h2>


                <div class="mt-[1.5vmax]">

                    <p class="text-[.7vmax] uppercase text-base-content/60">
                        Situação atual
                    </p>

                    <div class="mt-[.7vmax]">

                        <span class="
                            badge
                            rounded-full
                            px-[1vmax]
                            py-[1vmax]
                            text-[.8vmax]
                            font-semibold

                            {{ match($employee->status->value) {
                                'pending' => 'badge-warning',
                                'active' => 'badge-success',
                                'inactive' => 'badge-error',
                                'suspended' => 'badge-neutral',
                                default => 'badge-ghost',
                            } }}
                        ">

                            <span class="mr-[.3vmax] w-[.35vmax] h-[.35vmax] rounded-full bg-current"></span>

                            {{ $employee->status->label() }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ENDEREÇO --}}
        {{-- ========================================================= --}}

        <div class="card bg-base-100 shadow-md col-span-1 md:col-span-7">

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

                    @if($employee->street || $employee->number)

                        <p class="text-[1vmax] font-medium">
                            {{ $employee->street ?? '' }}

                            @if($employee->number)
                                , {{ $employee->number }}
                            @endif
                        </p>

                        <p class="text-[1vmax] mt-[.2vmax]">
                            {{ $employee->district ?? 'Bairro não informado' }}
                        </p>

                        <p class="text-[.9vmax] text-base-content/60 mt-[.2vmax]">

                            {{ $employee->city ?? 'Cidade não informada' }}

                            @if($employee->state)
                                — {{ $employee->state }}
                            @endif

                        </p>

                    @else

                        <p class="text-[.9vmax] text-base-content/50">
                            Endereço não informado.
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- HISTÓRICO --}}
        {{-- ========================================================= --}}

        <div class="card bg-base-100 shadow-md col-span-1 md:col-span-5">

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
                            Funcionário cadastrado
                        </p>

                        <p class="text-[.75vmax] text-base-content/60">
                            {{ $employee->created_at?->format('d/m/Y H:i') }}
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
                            {{ $employee->updated_at?->format('d/m/Y H:i') }}
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- INFORMAÇÕES ADICIONAIS --}}
        {{-- ========================================================= --}}

        @if($employee->roleSheet?->notes)

            <div class="card bg-base-100 shadow-md col-span-1 md:col-span-12">

                <div class="card-body p-[1.5vmax]">

                    <h2 class="
                        text-[1.1vmax]
                        font-bold
                        uppercase
                        text-error
                    ">
                        Informações adicionais
                    </h2>

                    <p class="
                        text-[.9vmax]
                        text-base-content/70
                        whitespace-pre-line
                        mt-[1vmax]
                    ">
                        {{ $employee->roleSheet->notes }}
                    </p>

                </div>

            </div>

        @endif

    </div>

</div>

</x-panel.director>