<x-panel.layout>

    <x-slot:panelTitle>Área do Coordenador</x-slot:panelTitle>
    <x-slot:panelMessage>Seja bem-vindo à área do coordenador do sistema Zuni!</x-slot:panelMessage>


    <x-slot:aside>
    {{-- Perfil --}}
        <li class="w-full">
            <a
                href="{{ route(auth()->user()->role->value.'.profile') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.profile') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img 
                class="w-8 h-8 mr-3 shrink-0 rounded-full"
                src="https://ui-avatars.com/api/?name={{ auth()->user()->name[0] ?? 'Sem nome' }}" 
                />
                {{auth()->user()->name}}
            </a>
        </li>

    {{-- Dashboard --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.index') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img 
                class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                style="{{ request()->routeIs('coordinator.index') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                src="{{ asset('images/icons/dashboard.svg') }}" 
                />
                Dashboard
            </a>
        </li>

        {{-- Matrículas --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.student.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.student.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img 
                class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                style="{{ request()->routeIs('coordinator.student.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                src="{{ asset('images/icons/enroll.svg') }}" 
                />
                Matrículas
            </a>
        </li>

        {{-- Matrículas --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.teacher.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.teacher.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <x-svg.icon.people
                    class="w-6 h-6 mr-3 shrink-0 brightness-0 invert group-hover:invert-0"
                    style="{{ request()->routeIs('coordinator.teacher.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                />
                Professores
            </a>

        </li>
        {{-- Salas --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.classroom.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.classroom.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <x-svg.icon.blackboard
                    class="w-6 h-6 mr-3 shrink-0 brightness-0 invert group-hover:invert-0"
                    style="{{ request()->routeIs('coordinator.classroom.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                />
                Salas de Aula
            </a>
        </li>

        {{-- Cronogramas --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.schedules.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.schedules.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img 
                class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                style="{{ request()->routeIs('coordinator.schedules.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                src="{{ asset('images/icons/schedule.svg') }}" 
                />
                Cronogramas
            </a>
        </li>

        {{-- Relatórios --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.report.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.report.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img 
                class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                style="{{ request()->routeIs('coordinator.report.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                src="{{ asset('images/icons/reports.svg') }}" 
                />
                Relatórios
            </a>
        </li>

        {{-- Mural --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.forum') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.forum') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img 
                class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                style="{{ request()->routeIs('coordinator.forum') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                src="{{ asset('images/icons/forum.svg') }}" 
                />
                Mural
            </a>
        </li>

        {{-- Chat --}}
        <li class="w-full">
            <a
                href="{{ route('coordinator.chat') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('coordinator.chat') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img 
                class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                style="{{ request()->routeIs('coordinator.chat') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                src="{{ asset('images/icons/chat.svg') }}" 
                />
                Chat
            </a>
        </li> 
    </x-slot:aside>
    
    {!! $slot !!}

</x-panel.layout>