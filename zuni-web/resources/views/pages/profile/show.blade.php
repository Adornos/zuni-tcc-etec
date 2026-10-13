@php
    /*
     * Tela única de perfil, igual para todos os roles. Só o conteúdo específico do role muda:
     *  - professor, coordenador e diretor: ficha profissional (formação, especialização, contratação);
     *  - responsável: alunos vinculados.
     */
    $role = $profile->role;
    $isEmployee = $sheet !== null;
    $gender = ['M' => 'Masculino', 'F' => 'Feminino', 'O' => 'Outro'][$profile->gender] ?? null;

    $statusClass = match ($profile->status->value) {
        'pending' => 'badge-warning',
        'active' => 'badge-success',
        'inactive' => 'badge-error',
        'suspended' => 'badge-neutral',
        default => 'badge-ghost',
    };

    $identifier = $sheet?->id ?? $profile->id;
    $address = collect([
        trim(($profile->street ?? '').($profile->number ? ', '.$profile->number : ''), ', '),
        $profile->district,
        trim(($profile->city ?? '').($profile->state ? ' - '.$profile->state : ''), ' -'),
    ])->filter()->implode(' · ');
@endphp

<x-dynamic-component :component="'panel.' . $role->value">

    <div class="col-span-4 row-span-4 min-w-0 space-y-3 sm:space-y-4">

        {{-- CABEÇALHO --}}
        <div class="card bg-base-100 shadow-md">
            <div class="card-body min-w-0 p-4 sm:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                    <div class="flex min-w-0 flex-col items-center gap-4 text-center md:flex-row md:text-left">
                        <div class="avatar placeholder shrink-0">
                            <div class="h-20 w-20 rounded-full bg-primary text-primary-content sm:h-24 sm:w-24">
                                <span class="text-3xl font-bold">{{ strtoupper(mb_substr($profile->name, 0, 1)) }}</span>
                            </div>
                        </div>

                        <div class="min-w-0">
                            <h1 class="break-words text-2xl font-bold sm:text-3xl">{{ $profile->name }}</h1>
                            <p class="mt-1 text-sm text-base-content/60">
                                {{ $role->label() }}
                                @if ($isEmployee && filled($sheet->formation))
                                    · {{ $sheet->formation }}
                                @endif
                                @if ($isEmployee && filled($sheet->registration))
                                    · Registro {{ $sheet->registration }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-row items-center justify-between gap-4 md:flex-col md:items-end">
                        <span class="badge rounded-full px-3 py-2 text-sm font-semibold {{ $statusClass }}">
                            <span class="mr-1 h-2 w-2 rounded-full bg-current"></span>
                            {{ $profile->status->label() }}
                        </span>

                        <span class="text-sm text-base-content/60">
                            Id Nº <strong class="text-base-content">{{ str_pad($identifier, 4, '0', STR_PAD_LEFT) }}</strong>
                        </span>

                        <a href="{{ route($role->value . '.profile.edit') }}" class="btn btn-primary min-h-11 text-base">
                            Editar perfil
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:gap-4 lg:grid-cols-2">

            {{-- DADOS PESSOAIS --}}
            <x-profile.card title="Dados pessoais">
                <x-profile.field label="Nome completo" :value="$profile->name" />
                <x-profile.field label="Data de nascimento"
                    :value="$profile->birth_date ? $profile->birth_date->format('d/m/Y').' ('.$profile->age.' anos)' : null" />
                <x-profile.field label="Sexo" :value="$gender" />
                <x-profile.field label="CPF" :value="$profile->cpf" />
                <x-profile.field label="RG" :value="$profile->rg" />
            </x-profile.card>

            {{-- CONTA E CONTATO --}}
            <x-profile.card title="Conta e contato">
                <x-profile.field label="Usuário" :value="$profile->username" />
                <x-profile.field label="E-mail" :value="$profile->email" />
                <x-profile.field label="Telefone" :value="$profile->phone" />
            </x-profile.card>

            {{-- ENDEREÇO --}}
            <x-profile.card title="Endereço" class="lg:col-span-2">
                <x-profile.field label="Endereço completo" :value="$address" class="sm:col-span-2" />
            </x-profile.card>

            {{-- ESPECÍFICO DO ROLE: ficha profissional --}}
            @if ($isEmployee)
                <x-profile.card title="Formação profissional" class="lg:col-span-2">
                    <x-profile.field label="Formação" :value="$sheet->formation" />
                    <x-profile.field label="Especialização" :value="$sheet->specialization" />
                    <x-profile.field label="Matrícula / Registro" :value="$sheet->registration" />
                    <x-profile.field label="Contratação" :value="$sheet->hire_date?->format('d/m/Y')" />
                </x-profile.card>
            @endif

            {{-- ESPECÍFICO DO ROLE: alunos vinculados --}}
            @if ($role->value === 'guardian')
                <div class="card bg-base-100 shadow-md min-w-0 lg:col-span-2">
                    <div class="card-body min-w-0 p-4 sm:p-6">
                        <h2 class="text-lg font-bold uppercase text-error">Alunos vinculados</h2>

                        <ul class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @forelse ($students as $student)
                                <li class="rounded-box border border-base-300 p-3">
                                    @if (Route::has('guardian.student.show'))
                                        <a href="{{ route('guardian.student.show', $student->student_id) }}" class="font-medium link link-hover">
                                            {{ $student->user?->name ?? 'Aluno' }}
                                        </a>
                                    @else
                                        <span class="font-medium">{{ $student->user?->name ?? 'Aluno' }}</span>
                                    @endif
                                    <p class="text-xs text-base-content/60">{{ $student->classroom?->name ?? 'Sem turma' }}</p>
                                </li>
                            @empty
                                <li class="text-sm text-base-content/60">Nenhum aluno vinculado.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @endif

            {{-- HISTÓRICO --}}
            <x-profile.card title="Histórico" class="lg:col-span-2">
                <x-profile.field label="Cadastro" :value="$profile->created_at?->format('d/m/Y H:i')" />
                <x-profile.field label="Última atualização" :value="$profile->updated_at?->format('d/m/Y H:i')" />
            </x-profile.card>

            {{-- INFORMAÇÕES ADICIONAIS (ficha profissional) --}}
            @if ($isEmployee && filled($sheet->notes))
                <div class="card bg-base-100 shadow-md min-w-0 lg:col-span-2">
                    <div class="card-body min-w-0 p-4 sm:p-6">
                        <h2 class="text-lg font-bold uppercase text-error">Informações adicionais</h2>
                        <p class="mt-3 whitespace-pre-line">{{ $sheet->notes }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-dynamic-component>
