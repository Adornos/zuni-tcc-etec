<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    <div class="col-span-4 row-span-4 space-y-4">

        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">Relatórios</h2>

            @can('create', \App\Models\Report::class)
                <a
                    href="{{ route(auth()->user()->role->value . '.report.create') }}"
                    class="btn bg-Csecondary text-white"
                >
                    + Criar relatório
                </a>
            @endcan
        </div>

        <livewire:search.report-list />

    </div>

</x-dynamic-component>
