<x-dashboard.layout>

    <x-slot:panelTitle>Area do Responsável</x-slot:panelTitle>
    <x-slot:panelMessage>Bem-vindo à area do reposnsável do sistema Zuni!</x-slot:panelMessage>


    <x-slot:aside>
    {{-- Perfil --}}
        <li>
            <a
                href="{{ route('guardian.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-person-circle"></i>
                Dashboard
            </a>
        </li>

    {{-- Perfil --}}
        <li>
            <a
                href="{{ route('guardian.profile') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-person-circle"></i>
                Perfil
            </a>
        </li>

        {{-- Cadastros --}}
        <li>
            <a
                href="{{ route('guardian.registered') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-journal-plus"></i>
                Cadastros
            </a>
        </li>

        {{-- Mural --}}
        <li>
            <a
                href=""
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-megaphone"></i>
                Mural
            </a>
        </li>

        {{-- Chat --}}
        <li>
            <a
                href=""
                class="hover:bg-white hover:text-Cprimary"
            >
                <i class="bi bi-chat-dots"></i>
                Chat
            </a>
        </li> 
    </x-slot:aside>
    
    <x-slot:dashboardInfo>{!! $dashboardInfo !!}</x-slot:dashboardInfo>
    

</x-dashboard.layout>