<x-dashboard.layoutRefactored>

    <x-slot:panelTitle>Área do Diretor</x-slot:panelTitle>
    <x-slot:panelMessage>Bem-vindo à área do diretor do sistema Zuni!</x-slot:panelMessage>


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
                href="{{ route('director.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/dashboard.svg') }}" 
                />
                Dashboard
            </a>
        </li>

    {{-- Funcionários --}}
        <li>
            <a
                href="{{ route('director.employee.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/dashboard.svg') }}" 
                />
                Funcionários
            </a>
        </li>


        {{-- Mural --}}
        <li>
            <a
                href="{{ route('director.forum') }}"
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
                href="{{ route('director.chat') }}"
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

</x-dashboard.layout>