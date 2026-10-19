<x-panel.layout>

    <x-slot:panelTitle>Área do Diretor</x-slot:panelTitle>
    <x-slot:panelMessage>Bem-vindo à área do diretor do sistema Zuni!</x-slot:panelMessage>

    <x-slot:aside>
        {{-- Perfil --}}
        <li class="w-full">
            <a
                href="{{ route(auth()->user()->role->value.'.profile') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('director.profile') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-8 h-8 mr-3 shrink-0 rounded-full"
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Sem nome') }}"
                    alt="Avatar de {{ auth()->user()->name ?? 'usuário' }}"
                />
                {{ auth()->user()->name }}
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('director.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('director.index') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('director.index') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/dashboard.svg') }}"
                    alt=""
                />
                Dashboard
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('director.employee.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('director.employee.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <x-svg.icon.people
                    class="w-6 h-6 mr-3 shrink-0 brightness-0 invert group-hover:invert-0"
                    style="{{ request()->routeIs('director.employee.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                />
                Funcionários
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('director.report.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('director.report.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('director.report.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/reports.svg') }}"
                    alt=""
                />
                Relatórios
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('director.forum') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('director.forum') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('director.forum') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/forum.svg') }}"
                    alt=""
                />
                Mural
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('director.chat') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('director.chat') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('director.chat') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/chat.svg') }}"
                    alt=""
                />
                Chat
            </a>
        </li>

    </x-slot:aside>

    {!! $slot !!}

</x-panel.layout>
