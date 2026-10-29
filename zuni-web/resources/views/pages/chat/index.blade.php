<x-chat.layout>

    @if (app(\App\Services\Chat\ChatPermissions::class)->participatesInChat(auth()->user()))
        <livewire:chat.inbox />
    @else
        <div class="col-span-4 row-span-4">
            <div class="alert">
                <span>O chat está disponível para professores, coordenadores e responsáveis.</span>
            </div>
        </div>
    @endif

</x-chat.layout>

