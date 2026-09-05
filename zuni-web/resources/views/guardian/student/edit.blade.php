<x-panel.guardian>

    <div class="col-span-4 row-span-4 min-h-0">

        <div class="h-full overflow-y-auto pr-1">

            <form
                action="{{ route('guardian.student.update', $student) }}"
                method="POST"
                class="grid grid-cols-1 lg:grid-cols-4 gap-4 pb-4"
            >

                @csrf
                @method('PUT')


                {{-- ==========================================
                    CABEÇALHO
                =========================================== --}}

                <div class="lg:col-span-4">

                    <div class="card bg-base-100 shadow-xl">

                        <div class="card-body">

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                <div>

                                    <h1 class="text-3xl font-bold">
                                        Editar aluno
                                    </h1>

                                    <p class="text-base-content/60">
                                        Atualize os dados de {{ $student->name }}
                                    </p>

                                </div>

                                <div class="flex gap-2">

                                    <a
                                        href="{{ url()->previous() }}"
                                        class="btn btn-ghost"
                                    >
                                        Cancelar
                                    </a>

                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Salvar alterações
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================
                    DADOS PESSOAIS
                =========================================== --}}

                <div class="lg:col-span-2">

                    <div class="card bg-base-100 shadow-xl h-full">

                        <div class="card-body">

                            <h2 class="card-title">
                                Dados pessoais
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                                {{-- Nome --}}
                                <fieldset class="fieldset md:col-span-2">

                                    <label class="fieldset-legend">
                                        Nome completo
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name', $student->name) }}"
                                        class="input input-bordered w-full"
                                        required
                                    >

                                    @error('name')
                                        <p class="text-error text-sm">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </fieldset>


                                {{-- Data de nascimento --}}
                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        Data de nascimento
                                    </label>

                                    <input
                                        type="date"
                                        name="birth_date"
                                        value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                {{-- Gênero --}}
                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        Gênero
                                    </label>

                                    <select
                                        name="gender"
                                        class="select select-bordered w-full"
                                    >

                                        <option value="">
                                            Não informado
                                        </option>

                                        <option
                                            value="M"
                                            @selected(old('gender', $student->gender) === 'M')
                                        >
                                            Masculino
                                        </option>

                                        <option
                                            value="F"
                                            @selected(old('gender', $student->gender) === 'F')
                                        >
                                            Feminino
                                        </option>

                                        <option
                                            value="O"
                                            @selected(old('gender', $student->gender) === 'O')
                                        >
                                            Outro
                                        </option>

                                    </select>

                                </fieldset>


                                {{-- CPF --}}
                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        CPF
                                    </label>

                                    <input
                                        type="text"
                                        name="cpf"
                                        value="{{ old('cpf', $student->cpf) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                {{-- RG --}}
                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        RG
                                    </label>

                                    <input
                                        type="text"
                                        name="rg"
                                        value="{{ old('rg', $student->rg) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                {{-- Telefone --}}
                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        Telefone
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone', $student->phone) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                {{-- Email --}}
                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        E-mail
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email', $student->email) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================
                    ENDEREÇO
                =========================================== --}}

                <div class="lg:col-span-2">

                    <div class="card bg-base-100 shadow-xl h-full">

                        <div class="card-body">

                            <h2 class="card-title">
                                Endereço
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                                <fieldset class="fieldset md:col-span-2">

                                    <label class="fieldset-legend">
                                        Rua
                                    </label>

                                    <input
                                        type="text"
                                        name="street"
                                        value="{{ old('street', $student->street) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        Número
                                    </label>

                                    <input
                                        type="text"
                                        name="number"
                                        value="{{ old('number', $student->number) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        Bairro
                                    </label>

                                    <input
                                        type="text"
                                        name="district"
                                        value="{{ old('district', $student->district) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        Cidade
                                    </label>

                                    <input
                                        type="text"
                                        name="city"
                                        value="{{ old('city', $student->city) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>


                                <fieldset class="fieldset">

                                    <label class="fieldset-legend">
                                        Estado
                                    </label>

                                    <input
                                        type="text"
                                        name="state"
                                        value="{{ old('state', $student->state) }}"
                                        class="input input-bordered w-full"
                                    >

                                </fieldset>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================
                    DADOS ESCOLARES
                =========================================== --}}

                <div class="lg:col-span-2">

                    <div class="card bg-base-100 shadow-xl h-full">

                        <div class="card-body">

                            <h2 class="card-title">
                                Dados escolares
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                                {{-- Matrícula --}}
                                <div>

                                    <p class="text-sm text-base-content/50">
                                        Matrícula
                                    </p>

                                    <p class="font-semibold mt-1">
                                        {{ $student->studentSheet?->registration_number ?? 'Não informada' }}
                                    </p>

                                </div>


                                {{-- Turma --}}
                                <div>

                                    <p class="text-sm text-base-content/50">
                                        Turma
                                    </p>

                                    <p class="font-semibold mt-1">
                                        {{ $student->studentSheet?->classroom?->name ?? 'Sem turma' }}
                                    </p>

                                </div>


                                {{-- Responsável --}}
                                <div class="md:col-span-2">

                                    <p class="text-sm text-base-content/50">
                                        Responsável
                                    </p>

                                    <p class="font-semibold mt-1">
                                        {{ $student->studentSheet?->guardian?->name ?? 'Não informado' }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================
                    NECESSIDADES ESPECÍFICAS
                =========================================== --}}

                <div class="lg:col-span-2">

                    <div class="card bg-base-100 shadow-xl h-full">

                        <div class="card-body">

                            <h2 class="card-title">
                                Necessidades específicas
                            </h2>

                            <div class="space-y-3 mt-4">

                                <label class="flex items-center justify-between p-4 rounded-box bg-base-200 cursor-pointer">

                                    <span>
                                        Neurodivergência
                                    </span>

                                    <input
                                        type="hidden"
                                        name="neurodivergent"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        name="neurodivergent"
                                        value="1"
                                        class="checkbox checkbox-primary"
                                        @checked(old(
                                            'neurodivergent',
                                            $student->studentSheet?->neurodivergent
                                        ))
                                    >

                                </label>


                                <label class="flex items-center justify-between p-4 rounded-box bg-base-200 cursor-pointer">

                                    <span>
                                        Alergia
                                    </span>

                                    <input
                                        type="hidden"
                                        name="allergy"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        name="allergy"
                                        value="1"
                                        class="checkbox checkbox-primary"
                                        @checked(old(
                                            'allergy',
                                            $student->studentSheet?->allergy
                                        ))
                                    >

                                </label>


                                <label class="flex items-center justify-between p-4 rounded-box bg-base-200 cursor-pointer">

                                    <span>
                                        Restrição alimentar
                                    </span>

                                    <input
                                        type="hidden"
                                        name="food_restriction"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        name="food_restriction"
                                        value="1"
                                        class="checkbox checkbox-primary"
                                        @checked(old(
                                            'food_restriction',
                                            $student->studentSheet?->food_restriction
                                        ))
                                    >

                                </label>


                                <label class="flex items-center justify-between p-4 rounded-box bg-base-200 cursor-pointer">

                                    <span>
                                        Cuidados especiais
                                    </span>

                                    <input
                                        type="hidden"
                                        name="special_care"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        name="special_care"
                                        value="1"
                                        class="checkbox checkbox-primary"
                                        @checked(old(
                                            'special_care',
                                            $student->studentSheet?->special_care
                                        ))
                                    >

                                </label>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================
                    OBSERVAÇÕES
                =========================================== --}}

                <div class="lg:col-span-4">

                    <div class="card bg-base-100 shadow-xl">

                        <div class="card-body">

                            <h2 class="card-title">
                                Observações
                            </h2>

                            <fieldset class="fieldset mt-2">

                                <textarea
                                    name="notes"
                                    class="textarea textarea-bordered w-full min-h-32"
                                    placeholder="Observações sobre o aluno..."
                                >{{ old('notes', $student->studentSheet?->notes) }}</textarea>

                            </fieldset>

                        </div>

                    </div>

                </div>


                {{-- ==========================================
                    BOTÕES
                =========================================== --}}

                <div class="lg:col-span-4 flex justify-end gap-2">

                    <a
                        href="{{ back() }}"
                        class="btn btn-ghost"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Salvar alterações
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-panel.guardian>