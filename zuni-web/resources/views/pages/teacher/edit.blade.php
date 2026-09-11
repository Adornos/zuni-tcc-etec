<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    @php

        /*
         * Roteamento dinâmico conforme o perfil autenticado
         */

        $role = auth()->user()->role->value ?? auth()->user()->role;

        $backRoute = match ($role) {
            'teacher' => 'teacher.profile',
            'coordinator' => 'coordinator.teacher.show',
            'director' => 'coordinator.employee.show',
            default => 'home',
        };

    @endphp

    

    <form
        action="{{ route('teacher.profile.update') }}"
        method="post"
        class="card bg-base-100 shadow-md col-span-4 row-span-4"

        x-data="{
                strictPassword: @js(config('auth.password_strict_validation')),

                password: '',
                confirmation: '',

                get passwordValid() {
                    if (!this.strictPassword) {
                        return true;
                    }

                    return (
                        this.password.length >= 8 &&
                        /[a-z]/.test(this.password) &&
                        /[A-Z]/.test(this.password) &&
                        /\d/.test(this.password)
                    );
                },

                get passwordsEmpty() {
                    return this.password === '' && this.confirmation === '';
                },

                get passwordsMatch() {
                    return this.password === this.confirmation;
                },

                get canSubmit() {
                    return (
                        this.passwordsEmpty ||
                        (
                            this.passwordValid &&
                            this.passwordsMatch
                        )
                    );
                }
            }"

        @submit="if (!canSubmit) $event.preventDefault()"
    >

        @method('put')
        @csrf


        {{-- ========================================================= --}}
        {{-- PERFIL                                                     --}}
        {{-- ========================================================= --}}

        <div class="card-body">

            {{-- HEADER DO PERFIL --}}
            <div class="flex items-center gap-6 mb-6">

                <div class="avatar">
                    <div class="w-24 rounded-full">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($profile->name[0] ?? 'Sem nome') }}"
                            alt="Avatar"
                        />
                    </div>
                </div>

                <div>

                    <h2 class="card-title text-3xl">
                        {{ $profile->name ?? 'Sem nome' }}
                    </h2>

                    <p class="text-base-content/60">
                        Responsável
                    </p>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- INFORMAÇÕES PESSOAIS                                      --}}
            {{-- ========================================================= --}}

            <div class="divider">
                Informações Pessoais
            </div>

            <div class="grid grid-cols-4 gap-6">

                {{-- Nome --}}
                <label class="floating-label col-span-2">

                    <input
                        name="name"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('name', $profile->name) }}"
                        required
                    >

                    <span>Nome</span>

                </label>


                {{-- E-mail --}}
                <label class="floating-label col-span-2">

                    <input
                        name="email"
                        type="email"
                        class="input input-bordered w-full"
                        value="{{ old('email', $profile->email) }}"
                        required
                    >

                    <span>E-mail</span>

                </label>


                {{-- CPF --}}
                <label class="floating-label">

                    <input
                        name="cpf"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('cpf', $profile->cpf) }}"
                    >

                    <span>CPF</span>

                </label>


                {{-- RG --}}
                <label class="floating-label">

                    <input
                        name="rg"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('rg', $profile->rg) }}"
                    >

                    <span>RG</span>

                </label>


                {{-- Telefone --}}
                <label class="floating-label col-span-2">

                    <input
                        name="phone"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('phone', $profile->phone) }}"
                    >

                    <span>Telefone</span>

                </label>


                {{-- Data de nascimento --}}
                <label class="floating-label col-span-2">

                    <input
                        name="birth_date"
                        type="date"
                        class="input input-bordered w-full"
                        value="{{ old(
                            'birth_date',
                            $profile->birth_date
                                ? \Carbon\Carbon::parse($profile->birth_date)->format('Y-m-d')
                                : ''
                        ) }}"
                    >

                    <span>Data de nascimento</span>

                </label>


                {{-- Gênero --}}
                <label class="floating-label col-span-2">

                    <select
                        name="gender"
                        class="input input-bordered w-full"
                    >

                        <option value="">
                            Selecione
                        </option>

                        <option
                            value="M"
                            @selected(old('gender', $profile->gender) === 'M')
                        >
                            Masculino
                        </option>

                        <option
                            value="F"
                            @selected(old('gender', $profile->gender) === 'F')
                        >
                            Feminino
                        </option>

                        <option
                            value="O"
                            @selected(old('gender', $profile->gender) === 'O')
                        >
                            Outro
                        </option>

                    </select>

                    <span>Gênero</span>

                </label>

            </div>


            {{-- ========================================================= --}}
            {{-- ENDEREÇO                                                   --}}
            {{-- ========================================================= --}}

            <div class="divider">
                Endereço
            </div>

            <div class="grid grid-cols-3 gap-6">

                {{-- Rua --}}
                <label class="floating-label">

                    <input
                        name="street"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('street', $profile->street) }}"
                    >

                    <span>Rua</span>

                </label>


                {{-- Número --}}
                <label class="floating-label">

                    <input
                        name="number"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('number', $profile->number) }}"
                    >

                    <span>Número</span>

                </label>


                {{-- Bairro --}}
                <label class="floating-label">

                    <input
                        name="district"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('district', $profile->district) }}"
                    >

                    <span>Bairro</span>

                </label>


                {{-- Cidade --}}
                <label class="floating-label">

                    <input
                        name="city"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('city', $profile->city) }}"
                    >

                    <span>Cidade</span>

                </label>


                {{-- Estado --}}
                <label class="floating-label">

                    <input
                        name="state"
                        type="text"
                        class="input input-bordered w-full"
                        value="{{ old('state', $profile->state) }}"
                    >

                    <span>Estado</span>

                </label>

            </div>


            {{-- ========================================================= --}}
            {{-- SEGURANÇA                                                  --}}
            {{-- ========================================================= --}}

            <div class="divider mt-8">
                Segurança
            </div>

            <div class="grid grid-cols-2 gap-6">


                {{-- ===================================================== --}}
                {{-- SENHA                                                   --}}
                {{-- ===================================================== --}}

                <label class="flex flex-col floating-label">

                    <input
                        type="password"
                        name="password"

                        x-model="password"

                        class="input validator w-full"

                        :class="{
                            'input-error':
                                password !== '' && !passwordValid,

                            'input-success':
                                password !== '' && passwordValid
                        }"

                        placeholder="Senha"

                        :minlength="strictPassword ? 8 : null"

                        :pattern="strictPassword
                            ? '(?=.*\\d)(?=.*[a-z])(?=.*[A-Z]).{8,}'
                            : null"

                        :title="strictPassword
                            ? 'A senha precisa conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula e um número.'
                            : 'Digite qualquer senha.'"
                    >

                    <span>Senha</span>


                    {{-- Regras da senha --}}
                    <div
                        x-cloak x-show="password !== '' && !passwordValid"
                        x-transition
                        class="text-error text-sm mt-2 space-y-1"
                    >

                        <p x-cloak x-show="password.length < 8">
                            • Mínimo de 8 caracteres
                        </p>

                        <p x-cloak x-show="!/[A-Z]/.test(password)">
                            • Pelo menos uma letra maiúscula
                        </p>

                        <p x-cloak x-show="!/[a-z]/.test(password)">
                            • Pelo menos uma letra minúscula
                        </p>

                        <p x-cloak x-show="!/\d/.test(password)">
                            • Pelo menos um número
                        </p>

                    </div>


                    <p
                        x-cloak x-show="password !== '' && passwordValid"
                        x-transition
                        class="text-success text-sm mt-2"
                    >
                        Senha válida.
                    </p>

                </label>


                {{-- ===================================================== --}}
                {{-- CONFIRMAÇÃO                                             --}}
                {{-- ===================================================== --}}

                <label class="flex flex-col floating-label">

                    <input
                        type="password"
                        name="password_confirmation"

                        x-model="confirmation"

                        class="input validator w-full"

                        :class="{
                            'input-error':
                                confirmation !== '' && !passwordsMatch,

                            'input-success':
                                confirmation !== '' && passwordsMatch
                        }"

                        placeholder="Confirmação de senha"

                        title="Digite novamente a senha."
                    >

                    <span>Confirmação de senha</span>


                    {{-- Senhas diferentes --}}
                    <p
                        x-cloak x-show="confirmation !== '' && !passwordsMatch"
                        x-transition
                        class="text-error text-sm mt-2"
                    >
                        As senhas não correspondem.
                    </p>


                    {{-- Senhas iguais --}}
                    <p
                        x-cloak x-show="confirmation !== '' && passwordsMatch"
                        x-transition
                        class="text-success text-sm mt-2"
                    >
                        As senhas correspondem.
                    </p>

                </label>

            </div>


            {{-- ========================================================= --}}
            {{-- ROLE                                                        --}}
            {{-- ========================================================= --}}

            <!-- NÃO RETIRAR -->

            <label class="floating-label hidden">

                <select
                    name="role"
                    class="input input-bordered w-full"
                >

                    <option value="{{ $profile->role->value }}">
                        {{ $profile->role->label() }}
                    </option>

                </select>

                <span>Função</span>

            </label>


            {{-- ========================================================= --}}
            {{-- AÇÕES                                                       --}}
            {{-- ========================================================= --}}

            <div class="card-actions justify-end mt-8">

                <a
                    href="{{ url()->previous() }}"
                    class="px-5 py-2 rounded-full border-2 border-Cprimary text-Cprimary font-medium hover:bg-Cprimary hover:text-white transition"
                >
                    Cancelar
                </a>


                <button
                    type="submit"

                    :disabled="!canSubmit"

                    class="px-5 py-2 rounded-full bg-Csecondary text-white font-medium hover:bg-Csecondary-dark transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Salvar Alterações
                </button>

            </div>

        </div>

    </form>

</x-dynamic-component>