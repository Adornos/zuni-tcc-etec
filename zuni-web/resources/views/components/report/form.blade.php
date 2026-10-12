@props([
    'report' => null,
    'target' => null,
    'selected' => [],
    'sheets',
    'classrooms',
    'types',
    'prefix',
])

@php
    $isEdit = $report !== null;
    $currentTarget = old('target', $target?->value ?? 'students');

    $selectedStudents = collect(old('students', $currentTarget === 'students' ? $selected : []))
        ->map(fn ($v) => (int) $v)->all();
    $selectedClassrooms = collect(old('classrooms', $currentTarget === 'classrooms' ? $selected : []))
        ->map(fn ($v) => (int) $v)->all();

    $currentType = old('type', $report?->type?->value ?? $types[0]->value);
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route($prefix . '.report.update', $report) : route($prefix . '.report.store') }}"
    x-data="{ target: @js($currentTarget), q: '' }"
    class="space-y-6 col-span-4"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

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
        <div class="card-body space-y-4">

            <h2 class="card-title">{{ $isEdit ? 'Editar relatório' : 'Novo relatório' }}</h2>

            {{-- Título --}}
            <div class="form-control">
                <label class="label" for="title"><span class="label-text">Título</span></label>
                <input
                    id="title" name="title" type="text" maxlength="255" required
                    value="{{ old('title', $report?->title) }}"
                    class="input input-bordered w-full"
                    placeholder="Digite o título do relatório"
                >
            </div>

            {{-- Tipo --}}
            <div class="form-control">
                <label class="label" for="type"><span class="label-text">Tipo</span></label>
                <select id="type" name="type" class="select select-bordered w-full" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected($currentType === $type->value)>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Corpo --}}
            <div class="form-control">
                <label class="label" for="content"><span class="label-text">Corpo</span></label>
                <textarea
                    id="content" name="content" rows="8" required maxlength="20000"
                    class="textarea textarea-bordered w-full"
                    placeholder="Digite o corpo do relatório"
                >{{ old('content', $report?->content) }}</textarea>
            </div>

        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body space-y-4">

            <h2 class="card-title">Atribuição</h2>

            {{-- Categoria: na criação é escolhida; na edição é fixa --}}
            @if ($isEdit)
                <input type="hidden" name="target" value="{{ $report->target->value }}">
                <p class="text-sm text-base-content/70">
                    Categoria: <span class="badge badge-neutral">{{ $report->target->label() }}</span>
                    — não é possível trocar entre alunos e salas. Para retirar todas as atribuições,
                    inative o relatório.
                </p>
            @else
                <div class="flex flex-wrap gap-6" role="radiogroup" aria-label="Categoria de atribuição">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="target" value="students" class="radio radio-primary" x-model="target">
                        <span>Alunos</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="target" value="classrooms" class="radio radio-primary" x-model="target">
                        <span>Salas</span>
                    </label>
                </div>
            @endif

            {{-- Alunos --}}
            <fieldset x-show="target === 'students'" :disabled="target !== 'students'" class="space-y-3">
                <legend class="font-semibold mb-2">Selecione os alunos</legend>

                <input
                    type="search" x-model="q" placeholder="Filtrar por aluno ou turma"
                    class="input input-bordered w-full" aria-label="Filtrar alunos"
                >

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 max-h-96 overflow-y-auto">
                    @forelse ($sheets as $sheet)
                        <label
                            class="flex items-center gap-3 p-3 rounded-box border border-base-300 hover:bg-base-200 cursor-pointer"
                            x-show="q === '' || @js(\Illuminate\Support\Str::lower(($sheet->user->name ?? '') . ' ' . ($sheet->classroom->name ?? ''))).includes(q.toLowerCase())"
                        >
                            <input
                                type="checkbox" name="students[]" value="{{ $sheet->id }}"
                                class="checkbox checkbox-primary"
                                @checked(in_array($sheet->id, $selectedStudents, true))
                            >
                            <span class="min-w-0">
                                <span class="block font-medium truncate">{{ $sheet->user->name ?? 'Aluno' }}</span>
                                <span class="block text-xs text-base-content/60">
                                    {{ $sheet->classroom->name ?? 'Sem turma' }}
                                </span>
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-base-content/60 col-span-full">
                            Nenhum aluno disponível para você.
                        </p>
                    @endforelse
                </div>

                @error('students') <p class="text-error text-sm">{{ $message }}</p> @enderror
                @error('students.*') <p class="text-error text-sm">{{ $message }}</p> @enderror
            </fieldset>

            {{-- Salas --}}
            <fieldset x-show="target === 'classrooms'" :disabled="target !== 'classrooms'" class="space-y-3">
                <legend class="font-semibold mb-2">Selecione as salas</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                    @forelse ($classrooms as $classroom)
                        <label class="flex items-center gap-3 p-3 rounded-box border border-base-300 hover:bg-base-200 cursor-pointer">
                            <input
                                type="checkbox" name="classrooms[]" value="{{ $classroom->id }}"
                                class="checkbox checkbox-primary"
                                @checked(in_array($classroom->id, $selectedClassrooms, true))
                            >
                            <span class="font-medium truncate">{{ $classroom->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-base-content/60 col-span-full">
                            Nenhuma sala disponível para você.
                        </p>
                    @endforelse
                </div>

                @error('classrooms') <p class="text-error text-sm">{{ $message }}</p> @enderror
                @error('classrooms.*') <p class="text-error text-sm">{{ $message }}</p> @enderror
            </fieldset>

        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a
            href="{{ $isEdit ? route($prefix . '.report.show', $report) : route($prefix . '.report.index') }}"
            class="btn btn-ghost"
        >
            Cancelar
        </a>
        <button type="submit" class="btn bg-Csecondary text-white">
            {{ $isEdit ? 'Salvar alterações' : 'Criar relatório' }}
        </button>
    </div>
</form>
