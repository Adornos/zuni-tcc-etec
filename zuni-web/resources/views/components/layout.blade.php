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
    <header class="w-full px-8 py-5 bg-linear-to-b from-white to-transparent">
        <div class="max-w-7xl mx-auto flex items-center justify-between">

            <!-- Logo -->
            <a href="/" class="flex items-center">
                <img src="{{ asset('images/logo.svg') }}" alt="Zuni" class="h-10">
            </a>

            <!-- Navegação -->
            <nav class="flex items-center gap-[1.5vmax]">

                <a href="#" class="font-medium text-Ctext hover:text-Cprimary-dark transition">
                    Sobre Nós
                </a>

                <a href="#" class="font-medium text-Ctext hover:text-Cprimary-dark transition">
                    Companhia
                </a>

                <!-- Parceiros -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0"
                        class="cursor-pointer font-medium text-Ctext hover:text-Cprimary-dark transition flex items-center gap-1">
                        Parceiros
                        <span class="text-xs">▼</span>
                    </label>

                    <ul tabindex="0"
                        class="dropdown-content z-50 mt-3 w-56 rounded-2xl bg-white p-2 shadow-xl">
                        <li>
                            <a class="rounded-xl hover:bg-Cprimary-light p-3 block">
                                Escolas Parceiras
                            </a>
                        </li>
                        <li>
                            <a class="rounded-xl hover:bg-Cprimary-light p-3 block">
                                Seja um Parceiro
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="#" class="mr-[3vmax] font-medium text-Ctext hover:text-Cprimary-dark transition">
                    Ajuda
                </a>

                <!-- Login e Auth -->
                @auth
                    <span class="text-sm">
                        <a
                        @switch(auth()->user()->role->value)
                            @case('student')    href="{{ route('student.index') }}"     @break 
                            @case('guardian')   href="{{ route('guardian.index') }}"    @break 
                            @case('teacher')    href="{{ route('teacher.index') }}"     @break 
                            @case('admin')      href="{{ route('admin.index') }}"       @break 
                        @endswitch
                        class="px-5 py-2 rounded-full border-2 border-Cprimary text-Cprimary font-medium hover:bg-Cprimary hover:text-white transition"
                        >
                        {{  auth()->user()->name }}
                        </a>
                    </span>
                    <form action="{{ route('logout') }}" method="get" class="inline">
                    @csrf
                        <button type="submit" class="px-5 py-2 rounded-full bg-Csecondary text-white font-medium hover:brightness-110 transition">
                        Logout
                        </button>
                    </form>
                @else

                    <a href="{{route('login')}}"
                    class="px-5 py-2 rounded-full border-2 border-Cprimary text-Cprimary font-medium hover:bg-Cprimary hover:text-white transition">
                        Log-in
                    </a>

                    <!-- CTA Principal -->
                    <a href="{{route('register')}}"
                    class="px-5 py-2 rounded-full bg-Csecondary text-white font-medium hover:brightness-110 transition">
                        Sign-in
                    </a>
                    
                @endauth

            </nav>

        </div>
    </header>

    <main class="flex-1 container max-w-full">
        {{ $slot }}
    </main>

    <footer class="w-full h-screen bg-Cprimary-dark text-white flex flex-col">

        <div class="flex-1 max-w-7xl mx-auto w-full grid grid-cols-1 md:grid-cols-3 gap-20 p-20 [column-rule:2px_solid_#ccc]">

            <div class="py-10 space-y-6">

            <h2 class="text-xl font-bold leading-[3em]">
                Nossas redes sociais
            </h2>

            <div class="flex gap-12">
                <a href="#" class="w-10 h-10 flex items-center justify-center hover:bg-white/20 transition">
                <i class="fa-brands fa-square-facebook fa-3x"></i>
                </a>
                <a href="#" class="w-10 h-10 flex items-center justify-center hover:bg-white/20 transition">
                <i class="fa-brands fa-square-twitter fa-3x"></i>
                </a>
                <a href="#" class="w-10 h-10 flex items-center justify-center hover:bg-white/20 transition">
                <i class="fa-brands fa-square-youtube fa-3x"></i>
                </a>
            </div>

            <p class="text-sm text-white/80">
                Privacy policy | Terms of service
            </p>

            <div class="text-sm space-y-1 text-white/80">
                <p>Copyright @ 2026 Zuni Software Limited | Registered in São Paulo SP, CEP</p>
                <p>Design feito por João Vitor Barbosa</p>
                <p>Programação: Guilherme Augusto e André Moura</p>
                <p>Textos e Pesquisa: Akemi</p>
            </div>

            </div>

            <div class="py-10">

            <h2 class="text-xl  font-bold leading-[3em]">
                Suporte e Documentação
            </h2>

            <ul class="space-y-2 text-white/80">
                <li>Documentação Técnica</li>
                <li>FAQ - Perguntas Frequentes</li>
                <li>Central de Ajuda</li>
                <li>Tutorial do site</li>
                <li>Tutorial do Aplicativo</li>
            </ul>

            </div>

            <div class="py-10 ">

            <h2 class="text-xl font-bold leading-[3em]">
                Contato e Serviços
            </h2>

            <ul class="space-y-2 text-white/80">
                <li>Termos de Uso</li>
                <li>Política de Privacidade</li>
                <li>Solicitar Demonstração</li>
            </ul>

            </div>

        </div>

    <div class=" p-6 text-center text-sm text-white/80">
        Zuni, Trabalho de Conclusão de Curso de Desenvolvimento de Sistemas da Escola Técnica Estadual de São Paulo, feito pelos alunos João Vitor Barbosa, Guilherme Augusto Adorno dos Passos, André de Moura Silva e Julia Akemi com objetivo de otimizar a gestão em sala de aula.
    </div>

    </footer>
    <script src="https://kit.fontawesome.com/c0becc67c4.js" crossorigin="anonymous"></script>
</body>

</html>