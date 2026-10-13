<x-panel.coordinator>

<form
    action="{{ route('coordinator.classroom.update', $classroom) }}"
    method="POST"
    class="space-y-6 col-span-4"
>
    @csrf
    @method('PUT')

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <div>
                <h2 class="card-title">Editar turma</h2>
                <p class="text-sm text-base-content/60">
                    {{ $classroom->name }} · Turma #{{ str_pad($classroom->id, 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <div class="divider"></div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <div class="form-control md:col-span-2">
                    <label class="label" for="name"><span class="label-text">Nome da turma *</span></label>
                    <input id="name" type="text" name="name" maxlength="50" required
                           value="{{ old('name', $classroom->name) }}"
                           class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label" for="grade"><span class="label-text">Série / Ano *</span></label>
                    <select id="grade" name="grade" class="select select-bordered w-full" required>
                        @foreach (App\Enums\ClassroomGrade::cases() as $grade)
                            <option value="{{ $grade->value }}" @selected(old('grade', $classroom->grade?->value) === $grade->value)>
                                {{ $grade->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control">
                    <label class="label" for="shift"><span class="label-text">Turno</span></label>
                    <select id="shift" name="shift" class="select select-bordered w-full">
                        <option value="">Não informado</option>
                        @foreach (App\Enums\ClassroomShift::cases() as $shift)
                            <option value="{{ $shift->value }}" @selected(old('shift', $classroom->shift?->value) === $shift->value)>
                                {{ $shift->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control">
                    <label class="label" for="capacity"><span class="label-text">Capacidade</span></label>
                    <input id="capacity" type="number" name="capacity" min="{{ max(1, $enrolled) }}"
                           value="{{ old('capacity', $classroom->capacity) }}"
                           class="input input-bordered w-full">
                    <p class="mt-1 text-xs text-base-content/50">
                        Matriculados agora: {{ $enrolled }}. Deixe em branco para não limitar.
                    </p>
                </div>

                <div class="form-control">
                    <label class="label" for="status"><span class="label-text">Status *</span></label>
                    <select id="status" name="status" class="select select-bordered w-full" required>
                        <option value="active" @selected(old('status', $classroom->status) === 'active')>Ativa</option>
                        <option value="inactive" @selected(old('status', $classroom->status) === 'inactive')>Inativa</option>
                    </select>
                </div>

            </div>

        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('coordinator.classroom.show', $classroom) }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn bg-Csecondary text-white">Salvar alterações</button>
    </div>
</form>

</x-panel.coordinator>
