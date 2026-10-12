<x-panel.coordinator>

    <div class="col-span-4 row-span-4 space-y-4">
        <div class="card bg-base-100 shadow">
            <div class="card-body space-y-3">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="card-title text-2xl">{{ $proficiency->name }}</h2>
                        <span class="badge mt-2 {{ $proficiency->is_active ? 'badge-success' : 'badge-error' }}">
                            {{ $proficiency->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('coordinator.proficiency.edit', $proficiency) }}" class="btn btn-sm btn-outline">Editar</a>
                        <form action="{{ route('coordinator.proficiency.status', $proficiency) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $proficiency->is_active ? 0 : 1 }}">
                            <button type="submit" class="btn btn-sm btn-outline">
                                {{ $proficiency->is_active ? 'Inativar' : 'Ativar' }}
                            </button>
                        </form>
                    </div>
                </div>

                <p class="text-base-content/80 whitespace-pre-line">{{ $proficiency->description ?: 'Sem descrição.' }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-2">
                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Alunos com a habilidade</div>
                        <div class="stat-value text-2xl">{{ $proficiency->student_proficiencies_count }}</div>
                    </div>
                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Observações (total)</div>
                        <div class="stat-value text-2xl">{{ $proficiency->observations_count }}</div>
                    </div>
                    <div class="stat bg-base-200 rounded-box">
                        <div class="stat-title">Observações válidas</div>
                        <div class="stat-value text-2xl">{{ $proficiency->valid_observations_count }}</div>
                    </div>
                </div>

                <p class="text-xs text-base-content/50">
                    Inativar uma habilidade impede novas observações, mas preserva valores e histórico dos alunos.
                </p>
            </div>
        </div>

        <a href="{{ route('coordinator.proficiency.index') }}" class="btn btn-ghost">← Voltar</a>
    </div>

</x-panel.coordinator>
