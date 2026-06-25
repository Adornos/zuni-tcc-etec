    {{-- Cadastrados (Crianças) --}}
    <div class="card bg-base-100 shadow-md row-span-2 flex-col items-center flex-wrap">
    <div class="card-body flex justify-center gap-[3vmax]">
        <div class="flex flex-row items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold">
                        {{count(auth()->user()->students()->latest()->get() ?? [])}}
                    </h2>

                    <p class="text-base-content/60">
                        Cadastrados
                    </p>
                </div>

                <span class="text-5xl">
                    👶
                </span>
            
        </div>
            <a 
            href="{{ route('guardian.registered') }}#cadastro"
            class="flex items-center text-[1vmax] pl-[0.6vmax] pr-[1.8vmax] py-[0.6vmax] rounded-full bg-Csecondary text-white font-medium hover:bg-Csecondary-dark transition">
                <h1 class="text-[4vmax] leading-0 mr-[1vmax]">+</h1>
                <p>Cadastrar <br> nova criança</p>
            </a>
    </div>
    </div>

    {{-- Notificações --}}
    <div class="card bg-base-100 shadow-md">
        <div class="card-body flex-row items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold">
                    5
                </h2>

                <p class="text-base-content/60">
                    Notificações
                </p>
            </div>

            <span class="text-5xl">
                🔔
            </span>
        </div>
    </div>

    {{-- Mural --}}
    <div class="card bg-base-100 shadow-md">
        <div class="card-body flex-row items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold">
                    3
                </h2>

                <p class="text-base-content/60">
                    Mural
                </p>
            </div>

            <span class="text-5xl">
                📌
            </span>
        </div>
    </div>