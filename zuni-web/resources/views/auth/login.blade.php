<x-layout>
    <x-slot:title>
        Login do Responsável
    </x-slot:title>

    <div class="hero min-h-[calc(100vh-16rem)] bg-Cprimary-light">
        <div class="hero-content flex-col">

            <div class="card w-full max-w-lg bg-Cwhite shadow-xl">

                <div class="card-body">

                    <h1 class="text-3xl font-bold text-center text-Cprimary mb-2">
                        Área de login
                    </h1>

                    <p class="text-center text-Ctext-muted mt-6">
                                               Crie sua conta para acompanhar seus alunos

                    </p>

                    <form
                        method="POST"
                        action="/login"

                        x-data="{
                            login: @js(old('login', '')),
                            password: '',
                            remember: false,

                            get canSubmit() {
                                return (
                                    this.login.trim() !== '' &&
                                    this.password !== ''
                                );
                            }
                        }"

                        @submit="if (!canSubmit) $event.preventDefault()"
                    >
                        @csrf


                        {{-- Email ou Username --}}
                        <label class="floating-label mt-6">
                            <input
                                type="text"
                                name="login"
                                x-model="login"
                                placeholder="Email ou Username"
                                class="input input-bordered w-full @error('login') input-error @enderror"
                                autocomplete="username"
                                required
                            >

                            <span>Email ou Username</span>
                        </label>

                        @error('login')
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
                                autocomplete="current-password"
                                required
                            >

                            <span>Senha</span>
                        </label>

                        @error('password')
                            <div class="label -mt-4">
                                <span class="label-text-alt text-error">
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror


                        {{-- Lembrar / Recuperação --}}
                        <div class="flex justify-between items-center mt-4 mb-6">

                            <label class="label cursor-pointer gap-2">

                                <input
                                    type="checkbox"
                                    name="remember"
                                    x-model="remember"
                                    class="checkbox checkbox-sm"
                                >

                                <span class="label-text">
                                    Lembrar-me
                                </span>

                            </label>


                            <a
                                href="/forgot-password"
                                class="text-sm text-Csecondary hover:underline"
                            >
                                Esqueci minha senha
                            </a>

                        </div>


                        {{-- Botão --}}
                        <button
                            type="submit"
                            class="btn bg-Csecondary hover:bg-Csecondary-dark border-none text-white w-full"
                            :disabled="!canSubmit"
                        >
                            Entrar
                        </button>

                    </form>


                    <div class="divider text-Ctext-muted">
                        OU
                    </div>


                    <p class="text-center text-sm">

                        Ainda não possui cadastro?

                        <a
                            href="/register"
                            class="text-Cprimary font-semibold hover:underline"
                        >
                            Criar conta
                        </a>

                    </p>

                </div>

            </div>

        </div>
    </div>

</x-layout>