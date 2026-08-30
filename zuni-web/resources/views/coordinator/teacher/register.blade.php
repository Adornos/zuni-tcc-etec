<form action="{{ route('coordinator.teacher.store') }}" method="POST" class="space-y-8 col-span-4">
    @csrf

    {{-- ========================================================= --}}
    {{-- DADOS PESSOAIS --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Dados pessoais
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Nome --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Nome completo *</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', 'Rafael Martins de Oliveira') }}"
                        placeholder="Ex.: Alex Anders Junior"
                        class="input input-bordered w-full"
                        required
                    />

                    @error('name')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{--Role--}}
                <input type="hidden" name="role" value="teacher">

                {{-- Data de nascimento --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Data de nascimento</span>
                    </label>

                    <input
                        type="date"
                        name="birth_date"
                        value="{{ old('birth_date', '1992-07-18') }}"
                        class="input input-bordered w-full"
                    />

                    @error('birth_date')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Gênero --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Gênero</span>
                    </label>

                    <select
                        name="gender"
                        class="select select-bordered w-full"
                    >
                        <option value="">Selecione</option>

                        <option value="M" @selected(old('gender', 'M') === 'M')>
                            Masculino
                        </option>

                        <option value="F" @selected(old('gender') === 'F')>
                            Feminino
                        </option>

                        <option value="O" @selected(old('gender') === 'O')>
                            Outro
                        </option>
                    </select>

                    @error('gender')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- DOCUMENTOS --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Documentos
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- CPF --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">CPF</span>
                    </label>

                    <input
                        type="text"
                        name="cpf"
                        value="{{ old('cpf', '529.847.316-20') }}"
                        placeholder="000.000.000-00"
                        class="input input-bordered w-full"
                    />

                    @error('cpf')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- RG --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">RG</span>
                    </label>

                    <input
                        type="text"
                        name="rg"
                        value="{{ old('rg', '42.781.936-5') }}"
                        placeholder="00.000.000-0"
                        class="input input-bordered w-full"
                    />

                    @error('rg')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- CONTATO --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Contato
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- E-mail --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">E-mail *</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', 'rafael.oliveira@zuni.test') }}"
                        placeholder="professor@exemplo.com"
                        class="input input-bordered w-full"
                        required
                    />

                    @error('email')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Telefone --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Telefone</span>
                    </label>

                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone', '(11) 98765-4321') }}"
                        placeholder="(00) 00000-0000"
                        class="input input-bordered w-full"
                    />

                    @error('phone')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- FORMAÇÃO PROFISSIONAL --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Formação profissional
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Formação --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Formação</span>
                    </label>

                    <input
                        type="text"
                        name="formation"
                        value="{{ old('formation', 'Licenciatura em Matemática') }}"
                        placeholder="Ex.: Licenciatura em Pedagogia"
                        class="input input-bordered w-full"
                    />

                    @error('formation')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Especialização --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Especialização</span>
                    </label>

                    <input
                        type="text"
                        name="specialization"
                        value="{{ old('specialization', 'Educação Matemática') }}"
                        placeholder="Ex.: Educação Infantil"
                        class="input input-bordered w-full"
                    />

                    @error('specialization')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Registro --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Matrícula / Registro</span>
                    </label>

                    <input
                        type="text"
                        name="registration"
                        value="{{ old('registration', 'PROF2026007') }}"
                        placeholder="Número de registro"
                        class="input input-bordered w-full"
                    />

                    @error('registration')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Data de contratação --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Data de contratação</span>
                    </label>

                    <input
                        type="date"
                        name="hire_date"
                        value="{{ old('hire_date', '2023-02-01') }}"
                        class="input input-bordered w-full"
                    />

                    @error('hire_date')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- ENDEREÇO --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Endereço
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                {{-- Rua --}}
                <div class="form-control md:col-span-4">
                    <label class="label">
                        <span class="label-text">Rua</span>
                    </label>

                    <input
                        type="text"
                        name="street"
                        value="{{ old('street', 'Rua das Palmeiras') }}"
                        class="input input-bordered w-full"
                    />

                    @error('street')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Número --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Número</span>
                    </label>

                    <input
                        type="text"
                        name="number"
                        value="{{ old('number', '318') }}"
                        class="input input-bordered w-full"
                    />

                    @error('number')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Bairro --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Bairro</span>
                    </label>

                    <input
                        type="text"
                        name="district"
                        value="{{ old('district', 'Vila Clementino') }}"
                        class="input input-bordered w-full"
                    />

                    @error('district')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Cidade --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Cidade</span>
                    </label>

                    <input
                        type="text"
                        name="city"
                        value="{{ old('city', 'São Paulo') }}"
                        class="input input-bordered w-full"
                    />

                    @error('city')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Estado --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Estado</span>
                    </label>

                    <input
                        type="text"
                        name="state"
                        value="{{ old('state', 'SP') }}"
                        class="input input-bordered w-full"
                    />

                    @error('state')
                        <span class="text-error text-sm mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- OBSERVAÇÕES --}}
    {{-- ========================================================= --}}

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Informações adicionais
            </h2>

            <div class="form-control">

                <label class="label">
                    <span class="label-text">
                        Observações
                    </span>
                </label>

                <textarea
                    name="notes"
                    rows="5"
                    placeholder="Informações adicionais sobre o professor..."
                    class="textarea textarea-bordered w-full"
                >{{ old('notes', 'Professor responsável pelas disciplinas de Matemática e acompanhamento de projetos de reforço escolar.') }}</textarea>

                @error('notes')
                    <span class="text-error text-sm mt-1">
                        {{ $message }}
                    </span>
                @enderror

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- BOTÃO --}}
    {{-- ========================================================= --}}

    <div class="flex justify-end">
        <button
            type="submit"
            class="btn btn-primary"
        >
            Cadastrar professor
        </button>
    </div>

</form>

