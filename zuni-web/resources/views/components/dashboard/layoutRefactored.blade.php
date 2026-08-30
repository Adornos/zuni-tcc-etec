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

    <div class="grid h-screen grid-cols-[19.5vmax_1fr] grid-rows-[6.75vmax_1fr]">

        {{-- Aside --}}
        <aside class="row-span-2 bg-Csecondary text-primary-content flex flex-col">

            {{-- Logo --}}
            <div class="flex justify-center py-8">
                <img src="{{ asset('images/logo_white.svg') }}" alt="Logo" class="h-8">
            </div>

            {{-- Navegação --}}
            <div class="flex-1 px-3">

                <ul class="menu w-full gap-[2vmin] text-[1vmax]">

                    @isset($aside)
                        {!! $aside  !!}
                    @else
                        <div>Sem Aside</div>
                    @endisset

                </ul>

            </div>

            {{-- Rodapé --}}
            <div class="border-t border-primary-content/20 p-3">

                <ul class="menu w-full">

                    <li>
                        <a href="#">

                            <img src="{{ asset('images/icons/config.svg') }}" class="w-[1.6vmax] mr-[1vmax]" alt=""/>

                            Configurações
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('logout') }}" class="text-error">

                            <img src="{{ asset('images/icons/logout.svg') }}" class="w-[1.6vmax] mr-[1vmax]" alt=""/>

                            Encerrar sessão
                        </a>
                    </li>

                </ul>

            </div>

        </aside>

        {{-- Header --}}
        <header class="bg-base-100 border-b px-3 py-3 sm:px-4 md:px-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div class="min-w-0">
                <h1 class="text-lg font-bold uppercase sm:text-xl md:text-2xl">
                    {!! isset($panelTitle) ? $panelTitle : 'Painel' !!}
                </h1>

                <p class="text-xs text-base-content/60 sm:text-sm">
                    {!! isset($panelMessage) ? $panelMessage : 'Seja bem-vindo novamente ao seu quadro de controle' !!}
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 sm:gap-4">

                {{-- Notificações --}}
                <button onclick="toggleNotifications()" class="btn btn-ghost btn-circle btn-sm sm:btn-md">
                    <div class="
                    max-sm:hidden indicator"
                    >
                        <span class="indicator-item badge badge-error badge-xs sm:badge-sm">
                            3
                        </span>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="#FFAE00" class="size-4 sm:size-5 md:size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.05c0 .243 0 .486-.002.729A8.967 8.967 0 013.69 15.77a23.848 23.848 0 005.454 1.31m5.713 0a24.255 24.255 0 01-5.713 0m5.713 0a3 3 0 11-5.713 0" />
                        </svg>
                    </div>
                </button>

                {{-- Perfil --}}
                <div class="max-sm:hidden dropdown dropdown-end" tabindex="0" role="button">
                    <div class="avatar flex items-center gap-2 rounded-xl bg-base-100 p-1 sm:p-2 md:p-[1vmax] cursor-pointer">
                        <div class="w-8 rounded-full sm:w-10 md:w-12">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Sem nome' }}" />
                        </div>
                        <span class="hidden sm:block min-w-0">
                            <span class="flex items-center gap-1">
                                <p class="truncate text-sm font-medium md:text-base">{{ auth()->user()->name ?? 'Sem nome' }}</p>
                                <span class="inline-flex">
                                    <div class="w-3 self-start">
                                        <img class="h-full object-contain"
                                            src="{{ asset('images/icons/chevronDown.svg') }}" alt="">
                                    </div>
                                </span>
                            </span>
                            <small class="block truncate text-[10px] text-Ctext-muted md:text-xs">{{ auth()->user()->email ?? 'sem email' }}</small>
                        </span>
                    </div>

                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow">

                        <li>
                            <a href="{{ route(auth()->user()->role->value.'.profile')}}">
                                Meu Perfil
                            </a>
                        </li>

                        {{-- <li>
                            <a href="#">
                                Configurações
                            </a>
                        </li> --}}

                        <li>
                            <a class="text-error" href="{{ route('logout') }}">
                                Sair
                            <img loading="lazy" src="{{ asset('images/icons/logout-red.svg') }}" class="w-[1.6vmax] ml-auto mr-[1vmax]" alt=""/>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </header>


        <main class="bg-base-200 p-6 overflow-auto bg-Cprimary-light">



            <div class="md:grid md:grid-cols-4 md:grid-rows-4 gap-4 h-full flex flex-col">


                @isset($slot)
                    {!! $slot !!}
                @else
                    <div>Sem Dashboard</div>
                @endisset

            </div>

        </main>

        {{-- Dashboard --}}

    </div>

    <script src="https://kit.fontawesome.com/c0becc67c4.js" crossorigin="anonymous"></script>
</body>

</html>