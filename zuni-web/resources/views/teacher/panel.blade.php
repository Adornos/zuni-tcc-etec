<x-panel.teacher>

    {{-- Grid especial 8x4 desta tela --}}
    <div class="col-span-4 row-span-4 grid xl:grid-cols-8 md:grid-cols-4 sm:grid-cols-1 xl:grid-rows-4 gap-4 ">

        {{-- Sala --}}
        <div class="card bg-base-100 shadow-md sm:col-span-1 md:col-span-2 xl:col-span-2 row-span-1">
            <div class="card-body flex-row items-center justify-center gap-10">
                <div>
                    <h2 class="text-3xl font-bold">
                        Sala 34
                    </h2>

                    <p class="text-base-content/60">
                        Próxima aula
                    </p>
                </div>

                <span class="text-5xl">
                    <x-svg.icon.people/>
                </span>
            </div>
        </div>

        {{-- Cantina --}}
        <div class="card bg-base-100 shadow-md text-center sm:col-span-1 md:col-span-2 xl:col-span-1 row-span-1 ">
            <div class="card-body">
                <div class="flex flex-row justify-center gap-10">
                    <div class="flex flex-col">
                    <h2 class="text-3xl font-bold">
                        R$155
                    </h2>

                    <p class="text-base-content/60 pt-[1vmin]">
                        Créditos da cantina
                    </p>
                    </div>
                    
                    <span class="sm:hidden">
                        <x-svg.icon.store/>
                    </span>
                </div>
            </div>
        </div>

        {{-- Novo Bimestre --}}
        <div class="card bg-base-100 shadow-md sm:col-span-1 md:col-span-2 xl:col-span-3 row-span-2">
            <div class="card-body">

                <h2 class="text-Cprimary text-3xl font-bold ">
                    Novo Bimestre!
                </h2>

                <p class="text-base-content/70 max-w-[15vmax] pt-[1vmax] pb-[1vmax] text-justify">
                    Atualize as habilidades que serão trabalhadas com cada turma este ano.
                    Organize o planejamento com antecedência e evite deixar tudo para a última hora.
                </p>

                <div class="card-actions mt-auto">
                    <button class="btn btn-primary">
                        Ir para Programação →
                    </button>
                </div>

            </div>
        </div>

        {{-- Agenda --}}
        <div class="card bg-base-100 shadow-md sm:col-span-1 md:col-span-2 xl:col-span-2 row-span-5">
            <div class="card-body overflow-y-auto">

                <div role="tablist" class="tabs tabs-bordered">
                    <a role="tab" class="tab tab-active">
                        Horários
                    </a>

                    <a role="tab" class="tab">
                        Eventos
                    </a>
                </div>

                @foreach(range(1, 6) as $evento)
                    <div class="flex gap-3 mt-4">

                        <div class="w-1 rounded bg-primary"></div>

                        <div>
                            <p class="font-semibold">
                                2º Ano Aula
                            </p>

                            <div class="flex gap-2 mt-1">
                                <span class="badge badge-primary">
                                    13:00
                                </span>

                                <span class="badge badge-primary">
                                    13:50
                                </span>
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>

        {{-- Reunião --}}
        <div class="card bg-base-100 shadow-md sm:col-span-1 md:col-span-2 xl:col-span-2 row-span-1">
            <div class="card-body flex-row items-center justify-center gap-10">

                <div>
                    <h2 class="text-3xl font-bold">
                        13/03
                    </h2>

                    <p class="text-base-content/60">
                        Próxima reunião
                    </p>
                </div>

                <span class="text-5xl text-Cprimary">
                    <x-svg.icon.blackboard/>
                </span>

            </div>
        </div>

        {{-- Mensagens --}}
        <div class="card bg-base-100 shadow-md text-center sm:col-span-1 md:col-span-2 xl:col-span-1 row-span-1">
            <div class="card-body">
                <h2 class="text-3xl font-bold">
                    10
                </h2>

                <p class="text-base-content/60">
                    Novas mensagens
                </p>
            </div>
        </div>

        {{-- Gráfico --}}
        <div class="card bg-base-100 shadow-md sm:col-span-1 md:col-span-2 xl:col-span-6 row-span-3">
            <div class="card-body">

                <h3 class="font-semibold text-lg">
                    Rendimento por Turma
                </h3>

                <span>
                    <x-svg.icon.graph class="w-full h-full"/>
                </span>
            </div>
        </div>

    </div>

</x-panel.teacher>