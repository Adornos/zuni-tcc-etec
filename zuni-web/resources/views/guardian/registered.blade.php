
<div class="card bg-white shadow-md col-span-5 row-span-5 flex">

    <div class="card-body">

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">
                Crianças cadastradas
            </h2>

            <span class="badge text-white bg-Csecondary">
                {{ count($students ?? []) }}
            </span>
        </div>

        <!-- Área scrollável -->
        <div class="space-y-3 overflow-y-auto max-h-[68vh] pr-2 bg-white p-[2vmax]">

            @forelse($students ?? [] as $student)

                <div
                    class="card-body flex flex-row items-center justify-between rounded-xl shadow-[0_0_20px_rgba(0,0,0,0.15)]">

                    <!-- Lado esquerdo: avatar + dados -->
                    <div class="flex items-center gap-6">

                        <!-- Avatar -->
                        <div class="avatar">
                            <div class="w-20 rounded-full">
                                <img src="https://ui-avatars.com/api/?name={{ $student->name }}" />
                            </div>
                        </div>

                        <!-- Informações principais -->
                        <div>
                            <h2 class="text-2xl font-bold">
                                {{ $student->name ?? 'Nome da criança' }}
                            </h2>

                            <p class="text-base-content/60">
                                {{ $student->class ?? 'Turma não definida' }} • {{ $student->shift ?? 'Turno não definido' }}
                            </p>

                            <div class="flex gap-2 mt-2">
                                <span class="badge text-white bg-Csecondary">
                                    Matrícula: {{ $student->registration ?? '0000' }}
                                </span>

                                <span class="badge text-white bg-Csecondary">
                                    {{ $student->age ?? '0' }} anos
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- Lado direito: status rápido -->
                    <div class="text-right">

                        <p class="text-sm text-base-content/60">
                            Situação
                        </p>

                        <p @class([
                                'text-lg font-semibold',
                                'text-green-600' => $student->status === 'ativo',
                                'text-red-600' => $student->status === 'inativo',
                                'text-amber-500' => !in_array($student->status, ['ativo', 'inativo']),
                            ])>
                            {{ ucfirst($student->status ?? 'pendente') }}
                        </p>


                        <div class="mt-2">
                            <span class="badge text-white bg-Csecondary">
                                Frequência: {{ $student->attendance ?? '0%' }}
                            </span>
                        </div>

                    </div>

                </div>

            @empty

                <div class="card-body flex flex-row items-center justify-between">

                    <!-- Lado esquerdo: avatar + dados -->
                    <div class="flex items-center gap-6">

                        <!-- Informações principais -->
                        <div>
                            <h2 class="text-2xl font-bold">
                                Nenhuma criança foi cadastrada
                            </h2>
                        </div>

                    </div>

                    <!-- Lado direito: status rápido -->

                    <div class="mt-4 flex justify-end">

                        <a href="{{ route('guardian.student.register') }}"
                            class="px-5 py-2 rounded-full bg-Csecondary text-white font-medium hover:bg-Csecondary-dark transition">

                            + Cadastrar criança

                        </a>

                    </div>

                </div>

            @endforelse

            @if (count($students ?? []))

                <div class="mt-4 flex justify-end ">

                    <a href="{{ route('guardian.student.register') }}"
                        id="cadastro"
                        class="px-5 py-2 rounded-full bg-Csecondary text-white font-medium hover:bg-Csecondary-dark transition">

                        + Cadastrar criança

                    </a>

                </div>

            @endif


        </div>

    </div>
</div>