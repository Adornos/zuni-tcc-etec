<x-panel.coordinator>
    {{-- Matrículas --}}
    <div class="card min-w-0 bg-base-100 shadow-md row-span-1 lg:row-span-2">
        <div class="card-body relative">
    
            {{-- Bolinha vermelha que deve aparecer apenas quando houver matrículas pendentes --}}
            <div class="absolute top-5 right-5 w-5 h-5 rounded-full bg-red-500"></div>
    
            <div class="flex flex-col items-center text-center pt-[.5vmax]">
                <h2 class="text-4xl font-Sans font-bold Text-Bold text-primary-dark pt-[1vmax]">
                    Matrículas
                </h2>
    
                <p class="text-base-content/70 mt-3 max-w-[200px] pt-[1vmax]">
                    Veja as recentes alterações nas matrículas
                </p>
            </div>
    
            <div class="card-actions mt-auto">
                <button class="btn btn-primary w-full bg-Cprimary" onclick="window.location.href='{{ route('coordinator.student.index') }}'"    >
                    <span>Ver Mais</span>
                    <span class="ml-auto text-xl">→</span>
                </button>
            </div>
    
        </div>
    </div>
    
    {{-- Cantina --}}
    <div class="card min-w-0 bg-base-100 shadow-md text-center pt-[1vmax]">
        <div class="card-body">
            <div class="flex flex-row items-center justify-center gap-4 md:gap-10">
                <div>
                    <h2 class="text-3xl font-bold">
                        10
                    </h2>
            
                    <p class="text-base-content/60">
                        Mensagens pendentes
                    </p>
                </div>

                <span class="text-5xl text-Cprimary">
                    <x-svg.icon.blackboard/>
                </span>
            </div>
        </div>
    </div>
    
    {{-- Novo Bimestre --}}
    <div class="card min-w-0 bg-base-100 shadow-md row-span-1 lg:row-span-2">
        <div class="card-body">
    
            <h2 class="text-4xl font-Sans font-bold Text-Bold text-primary-dark pt-[1vmax]">
                Novo Bimestre!
            </h2>
    
            <p class="text-base-content/70 pt-[1vmax]">
                Atualize as habilidades que serão trabalhadas com cada
                turma esse ano, não faça da última hora.
            </p>
    
            <div class="card-actions mt-auto">
                <a href="{{ route('coordinator.schedules.index') }}" class="btn btn-primary bg-Cprimary">
                    Ir para Programação →
                </a>
            </div>
    
        </div>
    </div>
    
    {{-- Agenda --}}
        
        @php
        $relatorios = [
            ['titulo' => 'Boletim 3º Bimestre',  'turma' => '5º Ano A', 'data' => '28/09/2026', 'status' => 'Concluído', 'resumo' => 'Notas e frequência de 32 alunos'],
            ['titulo' => 'Frequência de Setembro', 'turma' => '5º Ano A', 'data' => '30/09/2026', 'status' => 'Rascunho',  'resumo' => 'Faltas e justificativas do mês'],
            ['titulo' => 'Desempenho em Matemática', 'turma' => '4º Ano B', 'data' => '15/09/2026', 'status' => 'Concluído', 'resumo' => 'Comparativo entre as avaliações'],
            ['titulo' => 'Reunião de Pais 2º Bim.', 'turma' => '5º Ano A', 'data' => '02/08/2026', 'status' => 'Concluído', 'resumo' => 'Pontos discutidos e encaminhamentos'],
        ];
    @endphp

    <div class="card min-w-0 bg-base-100 shadow-md row-span-1 lg:row-span-4">
        <div class="card-body">

            <!-- Título da seção -->
            <div class="mb-6">
                <h2 class="text-4xl font-sans font-bold text-primary-dark pt-[1vmax]">
                    Relatórios
                </h2>

                <p class="mt-2 text-base-content/70 pt-[1vmax]">
                    Consulte e crie seus relatórios rapidamente nesta área!
                </p>
            </div>

            <!-- Botão criar -->
            <button type="button" class="btn btn-primary w-full gap-2 bg-Cprimary mb-5"">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Novo relatório
            </button>

            <div class="mt-6 flex min-h-0 flex-1 flex-col">
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-base-content/60">
                    Relatórios anteriores
                </h3>
                <div class="pt-[.5vmax]">
                        <div class="space-y-[.5vmax]">
                            <div class="flex items-start gap-2 p-3 rounded-lg  shadow border-l-4 border-gray-400">
                                <div class="w-1 bg-gray-400 rounded"></div>
                
                                <div>
                                    <div class="font-semibold">Sala 1º ano A</div>
                                    <div class="text-gray-500 text-sm">Relatório de desempenho da turma</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 p-3 rounded-lg  shadow border-l-4 border-gray-400">
                                <div class="w-1 bg-gray-400 rounded"></div>
                
                                <div>
                                    <div class="font-semibold">Sala 2º ano A</div>
                                    <div class="text-gray-500 text-sm">Relatório de desempenho da turma</div>
                            </div>
                        </div>
                    </div>
                </div>
   
            </div>

        </div>
    </div>
        
    
    
    {{-- Reunião --}}
    <div class="card min-w-0 bg-base-100 shadow-md text-center pt-[1vmax]">
        <div class="card-body">
            <div class="flex flex-row items-center justify-center gap-4 md:gap-10">
                <div>
                    <h2 class="text-3xl font-bold">
                        13/03
                    </h2>
                
        
                    <p class="text-base-content/60">
                        Próxima reunião
                    </p>
                </div>
                
                <span class="text-5xl">
                    <x-svg.icon.people/>
                </span>
            </div>
        </div>
    </div>
    
    {{-- Gráfico --}}
    <div class="card min-w-0 bg-base-100 shadow-md col-span-1 row-span-1 sm:col-span-2 lg:col-span-3 lg:row-span-2">
        <div class="card-body">
            <h3 class="font-semibold text-lg">
                Rendimento por Turma
            </h3>

            <span>
                <x-svg.icon.graph class="w-full h-full"/>
        </div>
    </div>
</x-panel.coordinator>