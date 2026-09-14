<x-panel.coordinator>

<form
    action="{{ route('coordinator.classroom.store') }}"
    method="POST"
    class="space-y-6 col-span-4"
>
    @csrf

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Criar turma
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Nome --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">
                            Nome da turma *
                        </span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ex.: 3º Ano A"
                        class="input input-bordered w-full"
                        required
                    >

                    @error('name')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                {{-- Série --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Série / Ano *
                        </span>
                    </label>

                    <input
                        type="text"
                        name="grade"
                        value="{{ old('grade') }}"
                        placeholder="Ex.: 3º Ano"
                        class="input input-bordered w-full"
                        required
                    >

                    @error('grade')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                {{-- Turno --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Turno
                        </span>
                    </label>

                    <select
                        name="shift"
                        class="select select-bordered w-full"
                    >
                        <option value="">Selecione</option>

                        <option value="morning"
                            @selected(old('shift') === 'morning')>
                            Manhã
                        </option>

                        <option value="afternoon"
                            @selected(old('shift') === 'afternoon')>
                            Tarde
                        </option>

                        <option value="full_time"
                            @selected(old('shift') === 'full_time')>
                            Integral
                        </option>

                        <option value="evening"
                            @selected(old('shift') === 'evening')>
                            Noite
                        </option>
                    </select>

                    @error('shift')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                {{-- Capacidade --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Capacidade
                        </span>
                    </label>

                    <input
                        type="number"
                        name="capacity"
                        value="{{ old('capacity') }}"
                        min="1"
                        placeholder="Ex.: 25"
                        class="input input-bordered w-full"
                    >

                    @error('capacity')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                {{-- Status --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">
                            Status
                        </span>
                    </label>

                    <select
                        name="status"
                        class="select select-bordered w-full"
                    >
                        <option value="active"
                            @selected(old('status', 'active') === 'active')>
                            Ativa
                        </option>

                        <option value="inactive"
                            @selected(old('status') === 'inactive')>
                            Inativa
                        </option>
                    </select>

                    @error('status')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

        </div>
    </div>


    <div class="flex justify-end">
        <button
            type="submit"
            class="btn btn-primary"
        >
            Criar turma
        </button>
    </div>

</form>

</x-panel.coordinator>