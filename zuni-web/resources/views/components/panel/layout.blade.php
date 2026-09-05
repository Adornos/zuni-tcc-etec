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

<body class="min-h-screen flex flex-col bg-white font-sans text-Cprimary">

    <div class="drawer lg:drawer-open">
        <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />
        
        <div class="drawer-content flex flex-col min-h-screen">
            {{-- Header --}}
            <header class="bg-base-100 border-b px-4 py-3 sm:px-6 flex items-center justify-between gap-4">

                {{-- Menu Mobile --}}
                <label for="sidebar-drawer" class="btn btn-ghost btn-circle lg:hidden shrink-0">
                    <img src="{{ asset('images/icons/gear.svg') }}" class="w-5 h-5" alt="Menu" />
                </label>

                {{-- Título --}}
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg font-bold uppercase tracking-tight sm:text-xl md:text-2xl">
                        {!! $panelTitle ?? 'Painel' !!}
                    </h1>

                    <p class="mt-0.5 truncate text-xs text-base-content/50 sm:text-sm">
                        {!! $panelMessage ?? 'Bem-vindo ao seu painel de controle.' !!}
                    </p>
                </div>

                {{-- Ações --}}
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">

                    {{-- Notificações --}}
                    <button
                        onclick="toggleNotifications()"
                        class="btn btn-ghost btn-circle btn-sm sm:btn-md"
                        aria-label="Notificações"
                    >
                        <div class="indicator">
                            <span class="indicator-item badge badge-error badge-xs sm:badge-sm">
                                3
                            </span>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="#FFAE00"
                                class="size-5 sm:size-6"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.05c0 .243 0 .486-.002.729A8.967 8.967 0 013.69 15.77a23.848 23.848 0 005.454 1.31m5.713 0a24.255 24.255 0 01-5.713 0m5.713 0a3 3 0 11-5.713 0"
                                />
                            </svg>
                        </div>
                    </button>

                                        @php
                    $currentUser = auth()->user();

                    $roleLabel = match ($currentUser?->role?->value ?? $currentUser?->role) {
                        'admin' => 'Administrador',
                        'director' => 'Diretor',
                        'coordinator' => 'Coordenador',
                        'teacher' => 'Professor',
                        'student' => 'Aluno',
                        'guardian' => 'Responsável',
                        'staff' => 'Funcionário',
                        default => 'Usuário',
                    };

                    @endphp

                    {{-- Perfil --}}

                    <div class="dropdown dropdown-end hidden sm:block">

                        {{-- Trigger --}}
                        <div
                            tabindex="0"
                            role="button"
                            class="flex cursor-pointer items-center gap-2 rounded-xl bg-base-100 p-1.5 transition hover:bg-base-200 md:p-2"
                        >

                            {{-- Avatar --}}
                            <div class="avatar placeholder shrink-0">

                                <div class="w-9 rounded-full md:w-10">
                                    <img
                                        src="https://ui-avatars.com/api/?name={{ urlencode($currentUser?->name ?? 'Sem nome') }}"
                                        alt="Avatar de {{ $currentUser?->name ?? 'usuário' }}"
                                        class="object-cover"
                                    />
                                </div>

                            </div>


                            {{-- Informações --}}
                            <div class="hidden min-w-0 md:block">

                                <div class="flex max-w-40 items-center gap-1.5">

                                    <p class="truncate text-sm font-semibold">
                                        {{ $currentUser?->name ?? 'Sem nome' }}
                                    </p>

                                    <img
                                        src="{{ asset('images/icons/chevronDown.svg') }}"
                                        class="h-3 w-3 shrink-0 opacity-60"
                                        alt=""
                                    >

                                </div>

                                <p class="mt-0.5 max-w-40 truncate text-[10px] text-Ctext-muted">
                                    {{ $roleLabel }}
                                </p>

                            </div>

                        </div>


                        {{-- Dropdown --}}
                        <ul
                            tabindex="0"
                            class="dropdown-content menu z-50 mt-2 w-64 rounded-2xl border border-base-200 bg-base-100 p-2 shadow-xl"
                        >

                            {{-- Cabeçalho do dropdown --}}
                            <li class="mb-1 pointer-events-none">

                                <div class="flex items-center gap-3 rounded-xl px-3 py-2">

                                    <div class="avatar placeholder shrink-0">

                                        <div class="w-10 rounded-full">
                                            <img
                                                src="https://ui-avatars.com/api/?name={{ urlencode($currentUser?->name ?? 'Sem nome') }}"
                                                alt=""
                                            >
                                        </div>

                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold">
                                            {{ $currentUser?->name ?? 'Sem nome' }}
                                        </p>

                                        <p class="truncate text-xs text-Ctext-muted">
                                            {{ $currentUser?->email ?? 'Sem e-mail' }}
                                        </p>

                                    </div>

                                </div>

                            </li>

                            <div class="divider my-1"></div>


                            {{-- Perfil --}}
                            <li>

                                <a
                                    href="{{ route(($currentUser?->role?->value ?? $currentUser?->role) . '.profile') }}"
                                    class="gap-3 rounded-xl"
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0"
                                        />
                                    </svg>

                                    <span>
                                        Meu perfil
                                    </span>

                                </a>

                            </li>


                            {{-- Sair --}}
                            <li>

                                <a
                                    href="{{ route('logout') }}"
                                    class="gap-3 rounded-xl text-error hover:bg-error/10 hover:text-error"
                                >

                                    <img
                                        loading="lazy"
                                        src="{{ asset('images/icons/logout-red.svg') }}"
                                        class="w-4"
                                        alt=""
                                    >

                                    <span>
                                        Sair
                                    </span>

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 bg-base-200 p-4 md:p-6 overflow-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:grid-rows-4 gap-4">
                    @isset($slot)
                        {!! $slot !!}
                    @else
                        <div>Sem Dashboard</div>
                    @endisset
                </div>
            </main>
        </div>

        {{-- Sidebar (Drawer) --}}
        <div class="drawer-side">
            <label for="sidebar-drawer" class="drawer-overlay"></label>
            
            <aside class="bg-Csecondary text-primary-content flex flex-col min-h-screen">

                {{-- Logo --}}
                <div class="flex justify-center py-6 md:py-8">
                    <img src="{{ asset('images/logo_white.svg') }}" alt="Logo" class="h-[2vmax] md:h-[2.5vmax]">
                </div>

                {{-- Navegação --}}
                <div class="flex-1 px-3 overflow-y-auto">
                    <ul class="menu w-full gap-2 md:gap-[2vmin] text-sm md:text-[1vmax]">
                        @isset($aside)
                            {!! $aside  !!}
                        @else
                            <div>Sem Aside</div>
                        @endisset
                    </ul>
                </div>

                {{-- Rodapé --}}
                <div class="border-t border-primary-content/20 p-2 md:p-3">
                    <ul class="menu w-full text-sm md:text-base">
                        <li>
                            <a href="#">
                                <img src="{{ asset('images/icons/config.svg') }}" class="w-4 md:w-5 mr-2 md:mr-3" alt=""/>
                                Configurações
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('logout') }}" class="text-error">
                                <img src="{{ asset('images/icons/logout.svg') }}" class="w-4 md:w-5 mr-2 md:mr-3" alt=""/>
                                Encerrar sessão
                            </a>
                        </li>
                    </ul>
                </div>

            </aside>
        </div>

    </div>

    <script src="https://kit.fontawesome.com/c0becc67c4.js" crossorigin="anonymous"></script>
</body>

</html>