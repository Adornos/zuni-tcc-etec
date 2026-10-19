<x-panel.coordinator>

    <div class="col-span-4 row-span-4 space-y-4">

        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">Habilidades</h2>
            <a href="{{ route('coordinator.proficiency.create') }}" class="btn bg-Csecondary text-white">+ Nova habilidade</a>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Estado</th>
                            <th class="text-right">Alunos</th>
                            <th class="text-right">Observações válidas</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proficiencies as $proficiency)
                            <tr>
                                <td>
                                    <a href="{{ route('coordinator.proficiency.show', $proficiency) }}" class="font-medium link link-hover">
                                        {{ $proficiency->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $proficiency->is_active ? 'badge-success' : 'badge-error' }}">
                                        {{ $proficiency->is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td class="text-right">{{ $proficiency->student_proficiencies_count }}</td>
                                <td class="text-right">{{ $proficiency->valid_observations_count }}</td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('coordinator.proficiency.edit', $proficiency) }}" class="btn btn-xs btn-outline">Editar</a>
                                        <form action="{{ route('coordinator.proficiency.status', $proficiency) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="is_active" value="{{ $proficiency->is_active ? 0 : 1 }}">
                                            <button type="submit" class="btn btn-xs btn-outline">
                                                {{ $proficiency->is_active ? 'Inativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-base-content/60">Nenhuma habilidade cadastrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $proficiencies->links() }}</div>
            </div>
        </div>
    </div>

</x-panel.coordinator>
