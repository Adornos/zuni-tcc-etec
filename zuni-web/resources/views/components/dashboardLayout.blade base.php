<!DOCTYPE html>
<html lang="en" data-theme="lofi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - Zuni' : 'Zuni'}}</title>
    <link rel="preconnect" href="<https://fonts.bunny.net>">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-white font-sans">

    <div class="grid h-screen grid-cols-[260px_1fr] grid-rows-[90px_1fr]">

        {{-- Aside --}}    
        <aside class="row-span-2 bg-Csecondary text-primary-content flex flex-col">

            {{-- Logo --}}
            <div class="flex justify-center py-8">
                <img src="{{ asset('images/logo_white.svg') }}" alt="Logo" class="h-8">
            </div>

            {{-- Navegação --}}
            <div class="flex-1 px-3">

                <ul class="menu w-full gap-1">

                    {{-- Perfil --}}
                    <li>
                        <a href="" class="py-3">

                            <i class="bi bi-person-circle text-2xl"></i>

                            <div>
                                <div class="font-semibold">
                                    Professor
                                </div>

                                <div class="text-xs opacity-70">
                                    Perfil
                                </div>
                            </div>

                        </a>
                    </li>

                    {{-- Dashboard --}}
                    <li>
                        <a href="" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                            <i class="bi bi-speedometer2 text-xl"></i>

                            Dashboard
                        </a>
                    </li>

                    {{-- Programação --}}
                    <li>
                        <a href="" class="{{ request()->routeIs('studentRegister') ? 'active' : '' }}">

                            <i class="bi bi-calendar3 text-xl"></i>

                            Programação
                        </a>
                    </li>

                    {{-- Relatórios --}}
                    <li>
                        <a href="#">

                            <i class="bi bi-journal-text text-xl"></i>

                            Relatórios
                        </a>
                    </li>

                    {{-- Comunicados --}}
                    <li>
                        <a href="">

                            <i class="bi bi-chat-square-text text-xl"></i>

                            Comunicados
                        </a>
                    </li>

                </ul>

            </div>

            {{-- Rodapé --}}
            <div class="border-t border-primary-content/20 p-3">

                <ul class="menu w-full">

                    <li>
                        <a href="#">

                            <i class="bi bi-gear text-lg"></i>

                            Configurações
                        </a>
                    </li>

                    <li>
                        <a href="/logout" class="text-error">

                            <i class="bi bi-box-arrow-right text-lg"></i>

                            Encerrar sessão
                        </a>
                    </li>

                </ul>

            </div>

        </aside>

        {{-- Header --}}
        <header class="bg-base-100 border-b px-6 flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold uppercase">
                    Painel Administrativo
                </h1>

                <p class="text-sm text-base-content/60">
                    Seja bem-vindo novamente ao seu quadro de controle
                </p>
            </div>

            <div class="flex items-center gap-4">

                {{-- Notificações --}}
                <button class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <span class="indicator-item badge badge-error badge-sm">
                            3
                        </span>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.05c0 .243 0 .486-.002.729A8.967 8.967 0 013.69 15.77a23.848 23.848 0 005.454 1.31m5.713 0a24.255 24.255 0 01-5.713 0m5.713 0a3 3 0 11-5.713 0" />
                        </svg>
                    </div>
                </button>

                {{-- Perfil --}}
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-outline">
                        Perfil
                    </div>

                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow">

                        <li>
                            <a href="#">
                                Meu Perfil
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                Configurações
                            </a>
                        </li>

                        <li>
                            <a class="text-error" href="#">
                                Sair
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </header>


        <main class="bg-base-200 p-6 overflow-auto">



            <div class="grid grid-cols-4 grid-rows-4 gap-4 h-full">

                {{-- Sala --}}
                <div class="card bg-base-100 shadow-md">
                    <div class="card-body flex-row items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold">
                                Sala 34
                            </h2>

                            <p class="text-base-content/60">
                                Próxima aula
                            </p>
                        </div>

                        <span class="text-5xl">
                            👥
                        </span>
                    </div>
                </div>

                {{-- Cantina --}}
                <div class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        <h2 class="text-3xl font-bold">
                            R$155
                        </h2>

                        <p class="text-base-content/60">
                            Créditos da cantina
                        </p>
                    </div>
                </div>

                {{-- Novo Bimestre --}}
                <div class="card bg-base-100 shadow-md row-span-2">
                    <div class="card-body">

                        <h2 class="text-primary text-2xl font-bold">
                            Novo Bimestre!
                        </h2>

                        <p class="text-base-content/70">
                            Atualize as habilidades que serão trabalhadas com cada
                            turma esse ano, não faça da última hora.
                        </p>

                        <div class="card-actions mt-auto">
                            <button class="btn btn-primary">
                                Ir para Programação →
                            </button>
                        </div>

                    </div>
                </div>

                {{-- Agenda --}}
                <div class="card bg-base-100 shadow-md row-span-4">
                    <div class="card-body overflow-y-auto">

                        <div role="tablist" class="tabs tabs-bordered">
                            <a role="tab" class="tab tab-active">
                                Horários
                            </a>

                            <a role="tab" class="tab">
                                Eventos
                            </a>
                        </div>

                        @foreach(range(1,6) as $evento)
                        <div class="flex gap-3 mt-4">

                            <div class="w-1 rounded bg-primary"></div>

                            <div>
                                <p class="font-semibold">
                                    2º Ano Aula
                                </p>

                                <div class="flex gap-2 mt-1">
                                    <span class="badge badge-primary">
                                        13:00
                                    </span>

                                    <span class="badge badge-primary">
                                        13:50
                                    </span>
                                </div>
                            </div>

                        </div>
                        @endforeach

                    </div>
                </div>

                {{-- Reunião --}}
                <div class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        <h2 class="text-3xl font-bold">
                            13/03
                        </h2>

                        <p class="text-base-content/60">
                            Próxima reunião
                        </p>
                    </div>
                </div>

                {{-- Mensagens --}}
                <div class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        <h2 class="text-3xl font-bold">
                            10
                        </h2>

                        <p class="text-base-content/60">
                            Novas mensagens
                        </p>
                    </div>
                </div>

                {{-- Gráfico --}}
                <div class="card bg-base-100 shadow-md col-span-3 row-span-2">
                    <div class="card-body">
                        <h3 class="font-semibold text-lg">
                            Rendimento por Turma
                        </h3>

                        <div class="flex items-center justify-center h-full text-base-content/50">
                            Gráfico aqui
                        </div>
                    </div>
                </div>

            </div>

        </main>

        {{-- Dashboard --}}

    </div>

    <script src="https://kit.fontawesome.com/c0becc67c4.js" crossorigin="anonymous"></script>
</body>

</html>