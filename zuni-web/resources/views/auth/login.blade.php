<x-layout>
    <x-slot:title>
        Login do Responsável
    </x-slot:title>
<div class="hero min-h-[calc(100vh-16rem)] bg-Cprimary-light">
    <div class="hero-content flex-col">

        <div class="card w-96 bg-Cwhite shadow-xl">

            <div class="card-body">

                <h1 class="text-3xl font-bold text-center text-Cprimary mb-2">
                    Área do Responsável
                </h1>

                <p class="text-center text-Ctext-muted mb-6">
                    Entre para acompanhar seus alunos
                </p>

                <form method="POST" action="/login">
                    @csrf

                    <!-- Email ou Username -->
                    <label class="floating-label mb-6">
                        <input
                            type="text"
                            name="login"
                            placeholder="Email ou Username"
                            value="{{ old('login') }}"
                            class="input input-bordered w-full @error('login') input-error @enderror"
                            required
                        >

                        <span>Email ou Username</span>
                    </label>

                    @error('email')
                        <div class="label -mt-4 mb-2">
                            <span class="label-text-alt text-error">
                                {{ $message }}
                            </span>
                        </div>
                    @enderror

                    <!-- Senha -->
                    <label class="floating-label mb-2">
                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            class="input input-bordered w-full @error('password') input-error @enderror"
                            required
                        >

                        <span>Senha</span>
                    </label>

                    @error('password')
                        <div class="label mb-2">
                            <span class="label-text-alt text-error">
                                {{ $message }}
                            </span>
                        </div>
                    @enderror

                    <!-- Remember -->
                    <div class="flex justify-between items-center mt-4">

                        <label class="label cursor-pointer gap-2">
                            <input
                                type="checkbox"
                                name="remember"
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

                    <!-- Submit -->
                    <div class="form-control mt-8">

                        <button
                            type="submit"
                            class="btn bg-Csecondary hover:bg-Csecondary-dark border-none text-white w-full"
                        >
                            Entrar
                        </button>

                    </div>

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
