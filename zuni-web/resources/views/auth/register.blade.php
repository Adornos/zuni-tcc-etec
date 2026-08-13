<x-layout>
    <x-slot:title>
        Cadastro de Responsável
    </x-slot:title>

<div class="hero min-h-[calc(100vh-16rem)] bg-Cprimary-light">
    <div class="hero-content flex-col">

        <div class="card w-full max-w-lg bg-Cwhite shadow-xl">

            <div class="card-body">

                <h1 class="text-3xl font-bold text-center text-Cprimary mb-2">
                    Cadastro de Responsável
                </h1>

                <p class="text-center text-Ctext-muted mb-6">
                    Crie sua conta para acompanhar seus alunos
                </p>

                <form method="POST" action="/register">
                    @csrf

                    <!-- Nome -->
                    <label class="floating-label mb-6">
                        <input
                            type="text"
                            name="name"
                            placeholder="Nome Completo"
                            value="{{ old('name') ?? 'Aldebran' }}"
                            class="input input-bordered w-full @error('name') input-error @enderror"
                            required
                        >

                        <span>Nome Completo</span>
                    </label>

                    @error('name')
                        <div class="label -mt-4 mb-2">
                            <span class="label-text-alt text-error">
                                {{ $message }}
                            </span>
                        </div>
                    @enderror

                    <!-- CPF -->
                    <label class="floating-label mb-6">
                        <input
                            type="text"
                            name="cpf"
                            placeholder="000.000.000-00"
                            value="{{ old('cpf') ?? '123.456.789-12'}}"
                            class="input input-bordered w-full @error('cpf') input-error @enderror"
                            required
                        >

                        <span>CPF</span>
                    </label>

                    @error('cpf')
                        <div class="label -mt-4 mb-2">
                            <span class="label-text-alt text-error">
                                {{ $message }}
                            </span>
                        </div>
                    @enderror

                    <!-- Telefone -->
                    <label class="floating-label mb-6">
                        <input
                            type="text"
                            name="phone"
                            placeholder="(13) 99999-9999"
                            value="{{ old('phone') ?? '(13) 99999-9999'}}"
                            class="input input-bordered w-full @error('phone') input-error @enderror"
                            required
                        >

                        <span>Telefone</span>
                    </label>

                    @error('phone')
                        <div class="label -mt-4 mb-2">
                            <span class="label-text-alt text-error">
                                {{ $message }}
                            </span>
                        </div>
                    @enderror

                    <!-- Email -->
                    <label class="floating-label mb-6">
                        <input
                            type="email"
                            name="email"
                            placeholder="responsavel@email.com"
                            value="{{ old('email') ?? 'responsavel@email.com' }}"
                            class="input input-bordered w-full @error('email') input-error @enderror"
                            required
                        >

                        <span>E-mail</span>
                    </label>

                    @error('email')
                        <div class="label -mt-4 mb-2">
                            <span class="label-text-alt text-error">
                                {{ $message }}
                            </span>
                        </div>
                    @enderror

                    <!-- Senha -->
                    <label class="floating-label mb-6">
                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            value="zuni2026"
                            class="input input-bordered w-full @error('password') input-error @enderror"
                            required
                        >

                        <span>Senha</span>
                    </label>

                    @error('password')
                        <div class="label -mt-4 mb-2">
                            <span class="label-text-alt text-error">
                                {{ $message }}
                            </span>
                        </div>
                    @enderror

                    <!-- Confirmar Senha -->
                    <label class="floating-label mb-6">
                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            value="zuni2026"
                            class="input input-bordered w-full"
                            required
                        >

                        <span>Confirmar Senha</span>
                    </label>

                    <!-- Termos -->
                    <label class="label cursor-pointer justify-start gap-3 mb-4">

                        <input
                            type="checkbox"
                            class="checkbox checkbox-sm "
                            required
                        >

                        <span class="label-text">
                            Li e aceito os termos de uso
                        </span>

                    </label>

                    <!-- Botão -->
                    <div class="form-control mt-6">

                        <button
                            type="submit"
                            class="btn bg-Csecondary hover:bg-Csecondary-dark border-none text-white w-full"
                        >
                            Criar Conta
                        </button>

                    </div>

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
