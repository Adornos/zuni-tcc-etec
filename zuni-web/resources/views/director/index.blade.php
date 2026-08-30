<x-panel.director>

{{-- Indicadores --}}
<div class="col-span-1 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">
            <p class="text-sm text-base-content/60">Alunos</p>
            <p class="text-3xl font-bold">428</p>
            <p class="text-xs text-success">+12 este mês</p>
        </div>
    </div>
</div>

<div class="col-span-1 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">
            <p class="text-sm text-base-content/60">Professores</p>
            <p class="text-3xl font-bold">32</p>
            <p class="text-xs text-base-content/50">Ativos</p>
        </div>
    </div>
</div>

<div class="col-span-1 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">
            <p class="text-sm text-base-content/60">Coordenadores</p>
            <p class="text-3xl font-bold">4</p>
            <p class="text-xs text-base-content/50">Ativos</p>
        </div>
    </div>
</div>

<div class="col-span-1 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">
            <p class="text-sm text-base-content/60">Pendências</p>
            <p class="text-3xl font-bold text-warning">7</p>
            <p class="text-xs text-warning">Requer atenção</p>
        </div>
    </div>
</div>


{{-- Matrículas recentes --}}
<div class="col-span-2 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">

            <div class="flex justify-between">
                <h2 class="card-title">Matrículas recentes</h2>

                <a href="#" class="btn btn-ghost btn-sm">
                    Ver todas
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-sm">

                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Turma</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>João Silva</td>
                            <td>5º A</td>
                            <td>
                                <span class="badge badge-success">
                                    Aprovada
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>Maria Santos</td>
                            <td>3º B</td>
                            <td>
                                <span class="badge badge-warning">
                                    Pendente
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>Pedro Lima</td>
                            <td>7º A</td>
                            <td>
                                <span class="badge badge-success">
                                    Aprovada
                                </span>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>


{{-- Comunicados --}}
<div class="col-span-2 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">

            <h2 class="card-title">Comunicados</h2>

            <div class="flex flex-col gap-2">

                <div class="alert alert-info">
                    <span>
                        Reunião pedagógica amanhã.
                    </span>
                </div>

                <div class="alert alert-warning">
                    <span>
                        Conselho de classe na sexta.
                    </span>
                </div>

            </div>

        </div>
    </div>
</div>


{{-- Desempenho da escola --}}
<div class="col-span-2 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">

            <div class="flex justify-between">
                <h2 class="card-title">
                    Desempenho da escola
                </h2>

                <select class="select select-sm select-bordered">
                    <option>2026</option>
                    <option>2025</option>
                </select>
            </div>

            <div class="flex items-end gap-3 h-32">

                <div class="bg-primary/20 rounded-t w-full h-[55%]"></div>
                <div class="bg-primary/30 rounded-t w-full h-[68%]"></div>
                <div class="bg-primary/40 rounded-t w-full h-[74%]"></div>
                <div class="bg-primary/50 rounded-t w-full h-[82%]"></div>
                <div class="bg-primary rounded-t w-full h-[88%]"></div>

            </div>

            <div class="flex justify-between text-xs text-base-content/50">
                <span>1º bim.</span>
                <span>2º bim.</span>
                <span>3º bim.</span>
                <span>4º bim.</span>
                <span>Média</span>
            </div>

        </div>
    </div>
</div>


{{-- Atividades recentes --}}
<div class="col-span-2 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">

            <h2 class="card-title">
                Atividades recentes
            </h2>

            <ul class="timeline timeline-vertical">

                <li>
                    <div class="timeline-start text-xs">
                        09:30
                    </div>

                    <div class="timeline-middle">
                        ●
                    </div>

                    <div class="timeline-end">
                        <p class="font-medium">
                            Novo professor cadastrado
                        </p>

                        <p class="text-xs text-base-content/50">
                            Carlos Oliveira
                        </p>
                    </div>
                </li>

                <li>
                    <div class="timeline-start text-xs">
                        10:15
                    </div>

                    <div class="timeline-middle">
                        ●
                    </div>

                    <div class="timeline-end">
                        <p class="font-medium">
                            Matrícula aprovada
                        </p>

                        <p class="text-xs text-base-content/50">
                            João Silva
                        </p>
                    </div>
                </li>

                <li>
                    <div class="timeline-start text-xs">
                        11:40
                    </div>

                    <div class="timeline-middle">
                        ●
                    </div>

                    <div class="timeline-end">
                        <p class="font-medium">
                            Relatório publicado
                        </p>

                        <p class="text-xs text-base-content/50">
                            Coordenação pedagógica
                        </p>
                    </div>
                </li>

            </ul>

        </div>
    </div>
</div>


{{-- Calendário escolar --}}
<div class="col-span-2 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">

            <h2 class="card-title">
                Calendário escolar
            </h2>

            <div class="flex flex-col gap-3">

                <div class="flex items-center gap-3">
                    <div class="badge badge-primary">
                        02 SET
                    </div>

                    <span>
                        Reunião com professores
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="badge badge-secondary">
                        05 SET
                    </div>

                    <span>
                        Conselho de classe
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="badge badge-accent">
                        10 SET
                    </div>

                    <span>
                        Reunião de responsáveis
                    </span>
                </div>

            </div>

        </div>
    </div>
</div>


{{-- Equipe --}}
<div class="col-span-2 row-span-1">
    <div class="card bg-base-100 shadow-sm h-full">
        <div class="card-body">

            <div class="flex justify-between items-center">
                <h2 class="card-title">
                    Equipe escolar
                </h2>

                <button class="btn btn-ghost btn-sm">
                    Gerenciar
                </button>
            </div>

            <div class="stats stats-horizontal shadow">

                <div class="stat">
                    <div class="stat-title">
                        Professores
                    </div>

                    <div class="stat-value text-2xl">
                        32
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-title">
                        Coordenadores
                    </div>

                    <div class="stat-value text-2xl">
                        4
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-title">
                        Funcionários
                    </div>

                    <div class="stat-value text-2xl">
                        47
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

</x-panel.director>