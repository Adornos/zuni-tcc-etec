@props(['proficiency' => null])

@php($isEdit = $proficiency !== null)

<form
    method="POST"
    action="{{ $isEdit ? route('coordinator.proficiency.update', $proficiency) : route('coordinator.proficiency.store') }}"
    class="space-y-6 col-span-4"
>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card bg-base-100 shadow">
        <div class="card-body space-y-4">
            <h2 class="card-title">{{ $isEdit ? 'Editar habilidade' : 'Nova habilidade' }}</h2>

            <div class="form-control">
                <label class="label" for="name"><span class="label-text">Nome</span></label>
                <input id="name" name="name" type="text" maxlength="100" required
                       value="{{ old('name', $proficiency?->name) }}"
                       class="input input-bordered w-full" placeholder="Ex.: Coordenação motora fina">
            </div>

            <div class="form-control">
                <label class="label" for="description"><span class="label-text">Descrição</span></label>
                <textarea id="description" name="description" rows="4" maxlength="1000"
                          class="textarea textarea-bordered w-full"
                          placeholder="O que esta habilidade avalia">{{ old('description', $proficiency?->description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ $isEdit ? route('coordinator.proficiency.show', $proficiency) : route('coordinator.proficiency.index') }}"
           class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn bg-Csecondary text-white">{{ $isEdit ? 'Salvar alterações' : 'Cadastrar habilidade' }}</button>
    </div>
</form>
