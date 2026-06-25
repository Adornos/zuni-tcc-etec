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

<body class="min-h-screen bg-base-100">

    <div class="grid h-screen grid-cols-[260px_1fr] grid-rows-[90px_1fr]">

        {{-- Aside --}}
        <aside class="row-span-2 bg-primary text-primary-content flex flex-col">

            {{-- Logo --}}
            <div class="flex justify-center py-8">
                <img src="{{ asset('assets/logo_white.svg') }}" alt="Logo" class="h-14">
            </div>

            {{-- Navegação --}}
            <div class="flex-1 px-3">

                <ul class="menu w-full gap-1">

                    <li>
                        <a href="">
                            <i class="bi bi-house-door"></i>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="">
                            <i class="bi bi-person-circle"></i>
                            Perfil
                        </a>
                    </li>

                    <li>
                        <a href="">
                            <i class="bi bi-chat-square-text"></i>
                            Comunicados
                        </a>
                    </li>

                </ul>

            </div>

            {{-- Rodapé --}}
            <div class="border-t border-primary-content/20 p-3">

                <ul class="menu">

                    <li>
                        <a href="">
                            <i class="bi bi-gear"></i>
                            Configurações
                        </a>
                    </li>

                    <li>
                        <a href="/logout" class="text-error">
                            <i class="bi bi-box-arrow-right"></i>
                            Sair
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
                    Seja bem-vindo novamente
                </p>

            </div>

            <div class="flex items-center gap-4">

                {{-- Notificações --}}
                <button class="btn btn-ghost btn-circle">

                    <div class="indicator">

                        <span class="indicator-item badge badge-error badge-sm">
                            3
                        </span>

                        <i class="bi bi-bell text-xl"></i>

                    </div>

                </button>

                {{-- Perfil --}}
                <div class="dropdown dropdown-end">

                    <div tabindex="0" role="button" class="btn btn-outline">
                        Perfil
                    </div>

                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow">

                        <li>
                            <a href="">
                                Meu Perfil
                            </a>
                        </li>

                        <li>
                            <a href="">
                                Configurações
                            </a>
                        </li>

                        <li>
                            <a href="/logout" class="text-error">
                                Sair
                            </a>
                        </li>

                    </ul>

                </div>

            </div>

        </header>

        {{-- Dashboard --}}
        <main class="bg-base-200 p-6 overflow-auto">

            <div class="grid grid-cols-4 grid-rows-4 gap-4 min-h-full">

                <div class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        Card 1
                    </div>
                </div>

                <div class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        Card 2
                    </div>
                </div>

                <div class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        Card 3
                    </div>
                </div>

                <div class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        Card 4
                    </div>
                </div>

            </div>

        </main>

    </div>

</body>

</html>