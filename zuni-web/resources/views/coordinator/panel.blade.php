{{-- Matrículas --}}
<div class="card bg-base-100 shadow-md row-span-2">
    <div class="card-body relative">

        {{-- Bolinha vermelha que deve aparecer apenas quando houver matrículas pendentes --}}
        <div class="absolute top-5 right-5 w-5 h-5 rounded-full bg-red-500"></div>

        <div class="flex flex-col items-center text-center mt-4">
            <h2 class="text-4xl font-Sans font-bold Text-Bold text-primary-dark pt-[1vmax]">
                Matrículas
            </h2>

            <p class="text-base-content/70 mt-3 max-w-[180px]">
                Veja as recentes alterações nas matrículas
            </p>
        </div>

        <div class="card-actions mt-auto">
            <button class="btn btn-primary w-full">
                <span>Ver Mais</span>
                <span class="ml-auto text-xl">→</span>
            </button>
        </div>

    </div>
</div>

{{-- Cantina --}}
<div class="card bg-base-100 shadow-md text-center pt-[1vmax]">
    <div class="card-body">
        <h2 class="text-3xl font-bold">
            15/02
        </h2>

        <p class="text-base-content/60">
            Próxima reunião 
        </p>
    </div>
</div>

{{-- Novo Bimestre --}}
<div class="card bg-base-100 shadow-md row-span-2 ">
    <div class="card-body">

        <h2 class="text-4xl font-Sans font-bold Text-Bold text-primary-dark pt-[1vmax]">
            Novo Bimestre!
        </h2>

        <p class="text-base-content/70">
            Atualize as habilidades que serão trabalhadas com cada
            turma esse ano, não faça da última hora.
        </p>

        <div class="card-actions mt-auto">
            <button class="btn btn-primary">
                Ir para Programação →
            </button>
        </div>

    </div>
</div>

{{-- Agenda --}}
<div class="card bg-base-100 shadow-md row-span-4">
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
<div class="card bg-base-100 shadow-md text-center pt-[1vmax]">
    <div class="card-body">
        <h2 class="text-3xl font-bold">
            13/03
        </h2>

        <p class="text-base-content/60">
            Próxima reunião
        </p>
    </div>
</div>

{{-- Gráfico --}}
<div class="card bg-base-100 shadow-md col-span-3 row-span-2">
    <div class="card-body">
        <h3 class="font-semibold text-lg">
            Rendimento por Turma
        </h3>

        <div class="flex items-center justify-center h-full text-base-content/50">
            Gráfico aqui
        </div>
    </div>
</div>