<x-panel.layout>

    <x-slot:panelTitle>Área do Coordenador</x-slot:panelTitle>
    <x-slot:panelMessage>Seja bem-vindo à área do coordenador do sistema Zuni!</x-slot:panelMessage>


    <x-slot:aside>
    {{-- Perfil --}}
        <li>
            <a
                href="{{ route(auth()->user()->role->value.'.profile') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax] rounded-full " 
                src="https://ui-avatars.com/api/?name={{ auth()->user()->name[0] ?? 'Sem nome' }}" 
                />
                {{auth()->user()->name}}
            </a>
        </li>

    {{-- Dashboard --}}
        <li>
            <a
                href="{{ route('coordinator.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/dashboard.svg') }}" 
                />
                Dashboard
            </a>
        </li>

        {{-- Matrículas --}}
        <li>
            <a
                href="{{ route('coordinator.students.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/enroll.svg') }}" 
                />
                Matrículas
            </a>
        </li>

        {{-- Matrículas --}}
        <li>
            <a
                href="{{ route('coordinator.teacher.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/teacher.svg') }}" 
                />
                Professores
            </a>

        </li>
        {{-- Salas --}}
        <li>
            <a
                href="{{ route('coordinator.classroom.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/classroom.svg') }}" 
                />
                Salas de Aula
            </a>
        </li>

        {{-- Cronogramas --}}
        <li>
            <a
                href="{{ route('coordinator.schedules.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/schedule.svg') }}" 
                />
                Cronogramas
            </a>
        </li>

        {{-- Relatórios --}}
        <li>
            <a
                href="{{ route('coordinator.report.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/reports.svg') }}" 
                />
                Relatórios
            </a>
        </li>

        {{-- Mural --}}
        <li>
            <a
                href="{{ route('coordinator.forum') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/forum.svg') }}" 
                />
                Mural
            </a>
        </li>

        {{-- Chat --}}
        <li>
            <a
                href="{{ route('coordinator.chat') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/chat.svg') }}" 
                />
                Chat
            </a>
        </li> 
    </x-slot:aside>
    
    {!! $slot !!}

</x-panel.layout>