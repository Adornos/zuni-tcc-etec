<x-dynamic-component :component="'panel.' . auth()->user()->role->value" >


    @php
        $sheet = $student->studentSheet;
        $guardian = $sheet?->guardian;
        $classroom = $sheet?->classroom;
        $enrollment = $sheet?->enrollment;

        $gender = match ($student->gender) {
            'M' => 'Masculino',
            'F' => 'Feminino',
            'O' => 'Outro',
            default => 'Não informado',
        };

        $statusValue = $student->status?->value ?? $student->status;

        $status = match ($statusValue) {
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'pending' => 'Pendente',
            'suspended' => 'Suspenso',
            default => 'Não informado',
        };

        $statusClass = match ($statusValue) {
            'active' => 'badge-success',
            'inactive' => 'badge-neutral',
            'pending' => 'badge-warning',
            'suspended' => 'badge-error',
            default => 'badge-ghost',
        };

        $birthDate = $student->birth_date?->format('d/m/Y');

        $skills = [
            'Sociabilidade' => $sheet?->sociability,
            'Autonomia' => $sheet?->autonomy,
            'Engajamento' => $sheet?->engagement,
            'Comunicação' => $sheet?->communication,
            'Desenvolvimento motor' => $sheet?->motor_development,
        ];

        /*
         * Roteamento dinâmico conforme o perfil autenticado
         */
        $role = auth()->user()->role->value ?? auth()->user()->role;

        $backRoute = match ($role) {
            'guardian' => 'guardian.registered',
            'teacher' => 'teacher.students',
            'coordinator', 'director' => 'coordinator.student.index',
            default => 'home',
        };

        $editRoute = match ($role) {
            'guardian' => 'guardian.student.edit',
            'teacher' => 'teacher.student.edit',
            'coordinator' => 'coordinator.student.edit',
            'director' => 'director.student.edit',
            default => null,
        };
    @endphp


    <div class="col-span-4 row-span-4 min-h-0">
        <div class="h-full overflow-y-auto pr-1">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 pb-4">

                {{-- =====================================================
                    CABEÇALHO
                ====================================================== --}}
                <div class="lg:col-span-4">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-5">

                                {{-- AVATAR --}}
                                <div class="avatar placeholder shrink-0">
                                    <div class="bg-primary text-primary-content rounded-full w-24 h-24">
                                        <span class="text-3xl font-bold">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- IDENTIFICAÇÃO --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h1 class="text-3xl font-bold">
                                            {{ $student->name }}
                                        </h1>
                                        <span class="badge {{ $statusClass }} max-lg:hidden">
                                            {{ $status }}
                                        </span>
                                    </div>

                                    <p class="text-base-content/60 mt-1">
                                        Aluno
                                    </p>

                                    <div class="flex flex-wrap gap-2 mt-4">
                                        <span class="badge badge-outline">
                                            Matrícula: {{ $sheet?->registration_number ?? 'Não informada' }}
                                        </span>
                                        <span class="badge badge-outline">
                                            {{ $student->age ?? '—' }} anos
                                        </span>
                                        <span class="badge badge-outline">
                                            {{ $classroom?->name ?? 'Sem turma' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- AÇÕES --}}
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    @if(Route::has($backRoute))
                                        <a href="{{ route($backRoute) }}" class="btn btn-ghost">
                                            Voltar
                                        </a>
                                    @endif

                                    @if ($editRoute && Route::has($editRoute))
                                        <a href="{{ route($editRoute, $student) }}" class="btn btn-primary">
                                            Editar Cadastro
                                        </a>
                                    @endif

                                    
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                {{-- =====================================================
                    DADOS PESSOAIS
                ====================================================== --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl h-full">
                        <div class="card-body">
                            <h2 class="card-title">Dados pessoais</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                                <div>
                                    <p class="text-sm text-base-content/50">Nome completo</p>
                                    <p class="font-semibold mt-1">{{ $student->name }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Data de nascimento</p>
                                    <p class="font-semibold mt-1">{{ $birthDate ?? 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Idade</p>
                                    <p class="font-semibold mt-1">{{ $student->age ?? 'Não informado' }} anos</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Gênero</p>
                                    <p class="font-semibold mt-1">{{ $gender }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">CPF</p>
                                    <p class="font-semibold mt-1">{{ $student->cpf ?? 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">RG</p>
                                    <p class="font-semibold mt-1">{{ $student->rg ?? 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Usuário</p>
                                    <p class="font-semibold mt-1">{{ $student->username ?? 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Telefone</p>
                                    <p class="font-semibold mt-1">{{ $student->phone ?? 'Não informado' }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <p class="text-sm text-base-content/50">E-mail</p>
                                    <p class="font-semibold mt-1 break-all">{{ $student->email ?? 'Não informado' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- =====================================================
                    INFORMAÇÕES ESCOLARES
                ====================================================== --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl h-full">
                        <div class="card-body">
                            <h2 class="card-title">Informações escolares</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                                <div>
                                    <p class="text-sm text-base-content/50">Matrícula</p>
                                    <p class="font-semibold mt-1">{{ $sheet?->registration_number ?? 'Não informada' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Turma</p>
                                    <p class="font-semibold mt-1">{{ $classroom?->name ?? 'Sem turma' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Ano/Série</p>
                                    <p class="font-semibold mt-1">{{ $classroom?->grade ?? 'Não informado' }}</p>
                                </div>
                                
                                @if ($role === 'coordinator')

                                <div class="row-span-2">
                                    <p class="text-sm text-base-content/50">Status da Matrícula</p>
                                    <div class="mt-[.7vmax]">
    
                                        <span class="
                                            badge
                                            rounded-full
                                            px-[1vmax]
                                            py-[1vmax]
                                            text-[.8vmax]
                                            font-semibold
                
                                            {{ match($student->status->value) {
                                                'pending' => 'badge-warning',
                                                'active' => 'badge-success',
                                                'inactive' => 'badge-error',
                                                'suspended' => 'badge-neutral',
                                                default => 'badge-ghost',
                                            } }}
                                        ">
                
                                            <span class="mr-[.3vmax] w-[.35vmax] h-[.35vmax] rounded-full bg-current"></span>
                
                                            {{ $student->status->label()}}
                
                                        </span>
                
                                    </div>

                                    <div class="flex flex-row gap-3 mt-4">
                                        <form action="{{ route('coordinator.student.approve', ['enrollment' => $enrollment ]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                    
                                            <button
                                                type="submit"
                                                class="
                                                    btn
                                                    btn-primary
                                                    w-full
                                                    min-h-8
                                                    h-8
                                                    text-xs
                                            ">
                                                Aprovar matrícula
                                            </button>
                                        </form>
                    
                    
                                        <form action="{{ route('coordinator.student.reject', ['enrollment' => $sheet?->enrollment ]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                    
                                            <button
                                                type="submit"
                                                class="
                                                btn
                                                btn-outline
                                                btn-error
                                                w-full
                                                min-h-8
                                                h-8
                                                text-xs
                                            ">
                                                Rejeitar matrícula
                                            </button>
                                        </form>
                                    </div>
                                </div>
            
                
                                
                                @endif
                                <div>
                                    <p class="text-sm text-base-content/50">Turno</p>
                                    <p class="font-semibold mt-1">{{ $classroom?->shift ?? 'Não informado' }}</p>
                                </div>


                                <div class="md:col-span-2">
                                    <p class="text-sm text-base-content/50">Responsável</p>
                                    @if ($guardian)
                                        <p class="font-semibold mt-1">{{ $guardian->name }}</p>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @if ($guardian->phone)
                                                <span class="badge badge-outline">{{ $guardian->phone }}</span>
                                            @endif
                                            @if ($guardian->email)
                                                <span class="badge badge-outline">{{ $guardian->email }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <p class="font-semibold text-base-content/50 mt-1">Responsável não informado</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                {{-- =====================================================
                    ENDEREÇO
                ====================================================== --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl h-full">
                        <div class="card-body">
                            <h2 class="card-title">Endereço</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                                <div class="md:col-span-2">
                                    <p class="text-sm text-base-content/50">Logradouro</p>
                                    <p class="font-semibold mt-1">
                                        @if ($student->street)
                                            {{ $student->street }}{{ $student->number ? ', ' . $student->number : '' }}
                                        @else
                                            Não informado
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Bairro</p>
                                    <p class="font-semibold mt-1">{{ $student->district ?? 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Cidade</p>
                                    <p class="font-semibold mt-1">{{ $student->city ?? 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/50">Estado</p>
                                    <p class="font-semibold mt-1">{{ $student->state ?? 'Não informado' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- =====================================================
                    NECESSIDADES ESPECÍFICAS
                ====================================================== --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl h-full">
                        <div class="card-body">
                            <h2 class="card-title">Necessidades específicas</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                @foreach ([
                                    'Neurodivergência' => $sheet?->neurodivergent,
                                    'Alergia' => $sheet?->allergy,
                                    'Restrição alimentar' => $sheet?->food_restriction,
                                    'Cuidados especiais' => $sheet?->special_care,
                                ] as $label => $value)
                                    <div class="flex items-center justify-between gap-3 p-4 rounded-box bg-base-200">
                                        <span class="font-medium">{{ $label }}</span>
                                        @if ($value)
                                            <span class="badge badge-warning">Sim</span>
                                        @else
                                            <span class="badge badge-ghost">Não</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- OBSERVAÇÕES --}}
                            <div class="mt-5">
                                <p class="text-sm text-base-content/50 mb-2">Observações</p>
                                <div class="bg-base-200 rounded-box p-4 min-h-24">
                                    @if ($sheet?->notes)
                                        {{ $sheet->notes }}
                                    @else
                                        <span class="text-base-content/50">Nenhuma observação registrada.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- =====================================================
                    GRÁFICO DE RADAR
                ====================================================== --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl h-full">
                        <div class="card-body">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h2 class="card-title">Proficiências</h2>
                                    <p class="text-sm text-base-content/60 mt-1">
                                        Desenvolvimento atual do aluno.
                                    </p>
                                </div>
                                <span class="badge badge-primary">Escala 0–10</span>
                            </div>

                            <div class="relative w-full h-[350px] mt-4">
                                <canvas id="studentRadarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- =====================================================
                    INDICADORES (BARRAS DE PROGRESSO)
                ====================================================== --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl h-full">
                        <div class="card-body">
                            <h2 class="card-title">Indicadores</h2>

                            <div class="space-y-4 mt-4">
                                @foreach ($skills as $label => $value)
                                    @php
                                        $score = $value !== null ? (float) $value : 0;
                                        $percentage = min(max($score * 10, 0), 100);
                                    @endphp
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium">{{ $label }}</span>
                                            <span class="text-sm font-semibold">
                                                {{ $value !== null ? number_format($score, 1, ',', '.') : '—' }}
                                            </span>
                                        </div>
                                        <progress
                                            class="progress progress-primary w-full"
                                            value="{{ $percentage }}"
                                            max="100">
                                        </progress>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script executado para renderizar o Gráfico com Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('studentRadarChart');

            if (canvas) {
                const ctx = canvas.getContext('2d');

                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: [
                            'Sociabilidade',
                            'Autonomia',
                            'Engajamento',
                            'Comunicação',
                            'Des. motor'
                        ],
                        datasets: [{
                            label: 'Proficiências',
                            data: [
                                {{ $sheet?->sociability ?? 0 }},
                                {{ $sheet?->autonomy ?? 0 }},
                                {{ $sheet?->engagement ?? 0 }},
                                {{ $sheet?->communication ?? 0 }},
                                {{ $sheet?->motor_development ?? 0 }}
                            ],
                            fill: true,
                            backgroundColor: 'rgba(79, 70, 229, 0.20)',
                            borderColor: 'rgb(79, 70, 229)',
                            pointBackgroundColor: 'rgb(79, 70, 229)',
                            pointBorderColor: '#ffffff',
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: 'rgb(79, 70, 229)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                min: 0,
                                max: 10,
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 2
                                },
                                grid: {
                                    circular: true
                                },
                                angleLines: {
                                    display: true
                                },
                                pointLabels: {
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw}/10`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

</x-dynamic-component>