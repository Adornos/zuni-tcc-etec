<x-panel.layout>

    <x-slot:panelTitle>Área do Professor</x-slot:panelTitle>
    <x-slot:panelMessage>Bem-vindo à área do professor do sistema Zuni!</x-slot:panelMessage>


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
                {{ auth()->user()->name }}
            </a>
        </li>

    {{-- Dashboard --}}
        <li>
            <a
                href="{{ route('teacher.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/dashboard.svg') }}" 
                />
                Dashboard
            </a>
        </li>

    {{-- Salas --}}
        <li>
            <a
                href="{{ route('teacher.classroom.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <x-svg.icon.enroll/>
                Salas
            </a>
        </li>

    {{-- Cronograma --}}
        <li>
            <a
                href="{{ route('teacher.schedule') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/schedule.svg') }}" 
                />
                Cronograma
            </a>
        </li>

    {{-- Mural --}}
        <li>
            <a
                href="{{ route('teacher.forum') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <x-svg.icon.forum class="text-white" />
                Mural
            </a>
        </li>

    {{-- Chat --}}
        <li>
            <a
                href="{{ route('teacher.chat') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <x-svg.icon.chat class="text-white" />
                Chat
            </a>
        </li> 
    </x-slot:aside>
    
    {!! $slot !!}
    

</x-panel.layout>