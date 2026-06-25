<x-dashboard.layout>

    <x-slot:panelTitle>Area do Responsável</x-slot:panelTitle>
    <x-slot:panelMessage>Bem-vindo à area do reposnsável do sistema Zuni!</x-slot:panelMessage>


    <x-slot:aside>
    {{-- Perfil --}}
        <li>
            <a
                href="{{ route('teacher.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-person-circle"></i>
                Dashboard
            </a>
        </li>

    {{-- Perfil --}}
        <li>
            <a
                href="{{ route(auth()->user()->role->value.'.profile') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-person-circle"></i>
                Perfil
            </a>
        </li>

        {{-- Cadastros --}}
        <li>
            <a
                href="{{ route('teacher.schedule') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-journal-plus"></i>
                Cronograma
            </a>
        </li>

        {{-- Mural --}}
        <li>
            <a
                href="{{ route('teacher.forum') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-megaphone"></i>
                Mural
            </a>
        </li>

        {{-- Chat --}}
        <li>
            <a
                href="{{ route('teacher.chat') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-chat-dots"></i>
                Chat
            </a>
        </li> 
    </x-slot:aside>
    
    <x-slot:dashboardInfo>{!! $dashboardInfo !!}</x-slot:dashboardInfo>
    

</x-dashboard.layout>