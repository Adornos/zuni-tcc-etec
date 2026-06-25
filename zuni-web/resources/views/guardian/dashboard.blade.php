<x-dashboard.layout>

    <x-slot:panelTitle>Area do Responsável</x-slot:panelTitle>
    <x-slot:panelMessage>Bem-vindo à area do reposnsável do sistema Zuni!</x-slot:panelMessage>


    <x-slot:aside>
    {{-- Perfil --}}
        <li>
            <a
                href="{{ route(auth()->user()->role->value.'.profile') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax] rounded-full " 
                src="https://ui-avatars.com/api/?name={{ $profile->name[0] ?? 'Sem nome' }}" 
                />
                Perfil
            </a>
        </li>

    {{-- Dashboard --}}
        <li>
            <a
                href="{{ route('guardian.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/dashboard.svg') }}" 
                />
                Dashboard
            </a>
        </li>

        {{-- Cadastros --}}
        <li>
            <a
                href="{{ route('guardian.registered') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <img 
                class="w-[2.2vmax] mr-[1vmax]" 
                src="{{ asset('images/icons/enroll.svg') }}" 
                />
                Cadastros
            </a>
        </li>

        {{-- Mural --}}
        <li>
            <a
                href="{{ route('guardian.forum') }}"
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
                href="{{ route('guardian.chat') }}"
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
    
    <x-slot:dashboardInfo>{!! $dashboardInfo !!}</x-slot:dashboardInfo>
    

</x-dashboard.layout>