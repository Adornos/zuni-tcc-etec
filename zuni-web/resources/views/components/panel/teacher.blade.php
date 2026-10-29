<x-panel.layout>

    <x-slot:panelTitle>Área do Professor</x-slot:panelTitle>
    <x-slot:panelMessage>Bem-vindo à área do professor do sistema Zuni!</x-slot:panelMessage>

    <x-slot:aside>
        {{-- Perfil --}}
        <li class="w-full">
            <a
                href="{{ route(auth()->user()->role->value.'.profile') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('teacher.profile') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
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
                href="{{ route('teacher.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('teacher.index') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('teacher.index') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/dashboard.svg') }}"
                    alt=""
                />
                Dashboard
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('teacher.classroom.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('teacher.classroom.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <x-svg.icon.blackboard
                    class="w-6 h-6 mr-3 shrink-0 brightness-0 invert group-hover:invert-0"
                    style="{{ request()->routeIs('teacher.classroom.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                />
                Salas
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('teacher.report.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('teacher.report.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('teacher.report.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/reports.svg') }}"
                    alt=""
                />
                Relatórios
            </a>
        </li>

        <li class="w-full">
            <a
                href="{{ route('teacher.schedule') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('teacher.schedule') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('teacher.schedule') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/schedule.svg') }}"
                    alt=""
                />
                Cronograma
            </a>
        </li>

        <!-- <li class="w-full">
            <a
                href="{{ route('teacher.forum') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('teacher.forum') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('teacher.forum') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/forum.svg') }}"
                    alt=""
                />
                Mural
            </a>
        </li> -->

        <li class="w-full">
            <a
                href="{{ route('teacher.chat') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('teacher.chat') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('teacher.chat') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/chat.svg') }}"
                    alt=""
                />
                Chat
            </a>
        </li>

    </x-slot:aside>

    {!! $slot !!}

</x-panel.layout>
