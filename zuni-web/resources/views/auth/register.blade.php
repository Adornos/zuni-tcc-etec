<x-layout>
    <x-slot:title>
        Cadastro de Responsável
    </x-slot:title>

    <div class="hero min-h-[calc(100vh-16rem)] bg-Cprimary-light">
        <div class="hero-content flex-col">

            <div class="card w-full max-w-lg bg-Cwhite shadow-xl">

                <div class="card-body">

                    <h1 class="text-3xl font-bold text-center text-Cprimary mb-2">
                        Área de cadastro
                    </h1>

                    <p class="text-center text-Ctext-muted mt-6">
                        Crie sua conta para acompanhar seus alunos
                    </p>

                    <form
                        method="POST"
                        action="/register"

                        x-data="{
                            strictPassword: @js(config('auth.password_strict_validation')),

                            name: @js(old('name', '')),
                            cpf: @js(old('cpf', '')),
                            phone: @js(old('phone', '')),
                            email: @js(old('email', '')),

                            password: '',
                            confirmation: '',
                            terms: false,

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
                                    this.terms &&
                                    !this.passwordsEmpty &&
                                    this.passwordValid &&
                                    this.passwordsMatch
                                );
                            }
                        }"

                        @submit="if (!canSubmit) $event.preventDefault()"
                    >
                        @csrf

                        {{-- Nome --}}
                        <label class="floating-label">
                            <input
                                type="text"
                                name="name"
                                x-model="name"
                                placeholder="Nome Completo"
                                class="input input-bordered w-full @error('name') input-error @enderror"
                                required
                            >

                            <span>Nome Completo</span>
                        </label>

                        @error('name')
                            <div class="label -mt-4">
                                <span class="label-text-alt text-error">
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror


                        {{-- CPF --}}
                        <label class="floating-label mt-6">
                            <input
                                type="text"
                                name="cpf"
                                x-model="cpf"
                                x-mask="999.999.999-99"
                                placeholder="000.000.000-00"
                                class="input input-bordered w-full @error('cpf') input-error @enderror"
                                required
                            >

                            <span>CPF</span>
                        </label>

                        @error('cpf')
                            <div class="label -mt-4">
                                <span class="label-text-alt text-error">
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror


                        {{-- Telefone --}}
                        <label class="floating-label mt-6">
                            <input
                                type="text"
                                name="phone"
                                x-model="phone"
                                x-mask="(99) 99999-9999"
                                placeholder="(13) 99999-9999"
                                class="input input-bordered w-full @error('phone') input-error @enderror"
                                required
                            >

                            <span>Telefone</span>
                        </label>

                        @error('phone')
                            <div class="label -mt-4">
                                <span class="label-text-alt text-error">
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror


                        {{-- Email --}}
                        <label class="floating-label mt-6">
                            <input
                                type="email"
                                name="email"
                                x-model="email"
                                placeholder="responsavel@email.com"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                required
                            >

                            <span>E-mail</span>
                        </label>

                        @error('email')
                            <div class="label -mt-4">
                                <span class="label-text-alt text-error">
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror


                        {{-- Senha --}}
                        <label class="floating-label mt-6 mb-2">
                            <input
                                type="password"
                                name="password"
                                x-model="password"
                                placeholder="••••••••"
                                class="input input-bordered w-full @error('password') input-error @enderror"
                                :class="{
                                    'input-error':
                                        password &&
                                        !passwordValid
                                }"
                                required
                            >

                            <span>Senha</span>
                        </label>

                        {{-- Regras da senha --}}
                        <div
                            x-cloak

                            x-show="strictPassword && password"
                            x-transition
                            class="text-sm mb-4"
                        >
                            <p :class="password.length >= 8 ? 'text-success' : 'text-error'">
                                <span x-text="password.length >= 8 ? '✓' : '✕'"></span>
                                Mínimo de 8 caracteres
                            </p>

                            <p :class="/[a-z]/.test(password) ? 'text-success' : 'text-error'">
                                <span x-text="/[a-z]/.test(password) ? '✓' : '✕'"></span>
                                Uma letra minúscula
                            </p>

                            <p :class="/[A-Z]/.test(password) ? 'text-success' : 'text-error'">
                                <span x-text="/[A-Z]/.test(password) ? '✓' : '✕'"></span>
                                Uma letra maiúscula
                            </p>

                            <p :class="/\d/.test(password) ? 'text-success' : 'text-error'">
                                <span x-text="/\d/.test(password) ? '✓' : '✕'"></span>
                                Um número
                            </p>
                        </div>

                        @error('password')
                            <div class="label -mt-4">
                                <span class="label-text-alt text-error">
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror


                        {{-- Confirmar senha --}}
                        <label class="floating-label mb-2">
                            <input
                                type="password"
                                name="password_confirmation"
                                x-model="confirmation"
                                placeholder="••••••••"
                                class="input input-bordered w-full"
                                :class="{
                                    'input-error':
                                        confirmation &&
                                        !passwordsMatch
                                }"
                                required
                            >

                            <span>Confirmar Senha</span>
                        </label>

                        <p
                            x-cloak

                            x-show="confirmation && !passwordsMatch"
                            x-transition
                            class="label -mt-1 mb-4"
                        >
                            <span class="label-text-alt text-error">
                                As senhas não coincidem.
                            </span>
                        </p>


                        {{-- Termos --}}
                        <label class="label cursor-pointer justify-start gap-3 mb-4">

                            <input
                                type="checkbox"
                                name="terms"
                                x-model="terms"
                                class="checkbox checkbox-sm"
                                required
                            >

                            <span class="label-text">
                                Li e aceito os termos de uso
                            </span>

                        </label>


                        {{-- Botão --}}
                        <button
                            type="submit"
                            class="btn bg-Csecondary hover:bg-Csecondary-dark border-none text-white w-full"
                            :disabled="!canSubmit"
                        >
                            Criar Conta
                        </button>

                    </form>


                    <div class="divider text-Ctext-muted">
                        OU
                    </div>


                    <p class="text-center text-sm">

                        Já possui uma conta?

                        <a
                            href="/login"
                            class="text-Cprimary font-semibold hover:underline"
                        >
                            Fazer login
                        </a>

                    </p>

                </div>

            </div>

        </div>
    </div>

</x-layout>