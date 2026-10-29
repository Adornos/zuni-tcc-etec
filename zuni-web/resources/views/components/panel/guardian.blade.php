<x-panel.layout>

    <x-slot:panelTitle>Área do Responsável</x-slot:panelTitle>
    <x-slot:panelMessage>Seja bem-vindo à área do responsável do sistema Zuni!</x-slot:panelMessage>

    <x-slot:aside>
        {{-- Perfil --}}
        <li class="w-full">
            <a
                href="{{ route(auth()->user()->role->value.'.profile') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('guardian.profile') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
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
                href="{{ route('guardian.student.index') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('guardian.student.*') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('guardian.student.*') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/enroll.svg') }}"
                    alt=""
                />
                Cadastros
            </a>
        </li>

        <!-- <li class="w-full">
            <a
                href="{{ route('guardian.forum') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('guardian.forum') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('guardian.forum') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/forum.svg') }}"
                    alt=""
                />
                Mural
            </a>
        </li> -->

        <li class="w-full">
            <a
                href="{{ route('guardian.chat') }}"
                class="group w-full hover:bg-white hover:text-Cprimary"
                style="{{ request()->routeIs('guardian.chat') ? 'background-color: #fff; color: #0a155c; font-weight: 600;' : '' }}"
            >
                <img
                    class="w-6 h-6 mr-3 shrink-0 group-hover:[filter:brightness(0)_saturate(100%)]"
                    style="{{ request()->routeIs('guardian.chat') ? 'filter: brightness(0) saturate(100%);' : '' }}"
                    src="{{ asset('images/icons/chat.svg') }}"
                    alt=""
                />
                Chat
            </a>
        </li>

    </x-slot:aside>

    {!! $slot !!}

</x-panel.layout>
