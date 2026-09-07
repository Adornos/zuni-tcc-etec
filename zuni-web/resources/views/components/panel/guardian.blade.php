<x-panel.layout>

    <x-slot:panelTitle>Área do Responsável</x-slot:panelTitle>
    <x-slot:panelMessage>Seja bem-vindo à área do reposnsável do sistema Zuni!</x-slot:panelMessage>


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
                href="{{ route('guardian.index') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <x-svg.icon.forum class="mr-[1vmax]" />
                Dashboard
            </a>
        </li>

        {{-- Cadastros --}}
        <li>
            <a
                href="{{ route('guardian.registered') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <x-svg.icon.enroll class="mr-[1vmax]" />
                Cadastros
            </a>
        </li>

        {{-- Mural --}}
        <li>
            <a
                href="{{ route('guardian.forum') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <x-svg.icon.forum class="mr-[1vmax]" />
                Mural
            </a>
        </li>

        {{-- Chat --}}
        <li>
            <a
                href="{{ route('guardian.chat') }}"
                class="hover:bg-white hover:text-Cprimary"
            >
                <x-svg.icon.chat class="mr-[1vmax]" />
                Chat
            </a>
        </li> 
    </x-slot:aside>
    
    {!! $slot !!}
    

</x-panel.layout>