{{-- Matriculas --}}
<div class="card bg-base-100 shadow-md row-span-2 col-span-2 flex-col pt-[.7vmax] pl-[.7vmax] bg-Ccard">
    <div class="card-body flex flex-col items-start justify-evenly">
    <div class="flex flex-col">
        <div class="flex flex-row items-center">
            <span class="text-[2.2vmax] font-bold">
                Matrículas
            </span>
            <span class="text-[3.5vmax] font-bold leading-0">
            </span>
        </div>
        <div>
            <p class="text-base-content text-[1.1vmax]">
                Veja as recentes alterações nas matrículas
            </p>
        </div>
    </div>
        
        <div class="flex flex-row">
            <div class="flex flex-col flex-1 gap-[.5vmax]">
                <div class="indicator">
                    <!-- {{$enrollmentsPendents = 8 ?? 'hidden'}} 8 notificação bolinha -->
                    <span class="indicator-item badge badge-error {{$enrollmentsPendents ?? 'hidden'}}">{{$enrollmentsPendents ?? ''}}</span>
                    <a href="" class="text-[1vmax] bg-yellow-200 text-yellow-900 badge badge-xl badge-soft shadow-md w-fit transition-all hover:bg-yellow-500 hover:text-Cprimary-light">
                        Pendentes
                    </a>
                </div>
                <div class="indicator">
                    <!-- {{$enrollmentsNews = 8 ?? 'hidden'}} 8 notificação bolinha -->
                    <span class="indicator-item badge badge-error {{$enrollmentsNews ?? 'hidden'}}">{{$enrollmentsNews ?? ''}}</span>
                    <a href="" class="text-[1vmax] bg-green-300 text-green-900 badge badge-xl badge-soft shadow-md w-fit transition-all hover:bg-green-600 hover:text-Cprimary-light">
                        Novas
                    </a>
                </div>
                <div class="indicator">
                    <!-- {{$enrollmentsNotViewed = 8 ?? 'hidden'}} 8 notificação bolinha -->
                    <span class="indicator-item badge badge-error {{$enrollmentsNotViewed?? 'hidden'}}">{{$enrollmentsNotViewed ?? ''}}</span>
                    <a href="" class="text-[1vmax] bg-red-300 text-red-900 badge badge-xl badge-soft shadow-md w-fit transition-all hover:bg-red-600 hover:text-Cprimary-light">
                        Não vistas
                    </a>
                </div>
            </div>
            
            <div class="flex flex-row gap-[1vmax] flex-1 items-center">
                <h2 class="text-[1.5vmax] font-bold">Total de matriculas</h2>
                <h2 class="text-[3.5vmax] font-bold">{{ $totalEnrollments ??  0 }}</h2>
            </div>
        </div>

        
    </div>
</div>

{{-- Chat --}}
<div class="card bg-base-100 shadow-md row-span-2 flex-col pt-[.7vmax] pl-[.7vmax] bg-Ccard">
    <div class="card-body flex flex-col items-start justify-evenly">

        <div class="flex flex-col ">
            <div class="flex flex-row items-center">
                <span class="text-[2.2vmax] font-bold text-Cprimary">
                    Chat
                </span>
                <span class="text-[3.5vmax] font-bold leading-0">
                </span>
            </div>

            <div>
                <p class="text-base-content text-[1.1vmax]">
                    Veja suas novas mensagens
                </p>
            </div>
        </div>

        <div class="flex flex-col w-full gap-[1vmax]">

            <div class="flex flex-row items-center justify-between indicator">
                <span class="indicator-item badge badge-error {{ ($chatUnread  ?? 0) > 0 ? '' : 'hidden' }}">
                    {{ $chatUnread ?? 0 }}
                </span>
                <a href=""
                   class="text-[1vmax] badge badge-xl badge-soft bg-Cprimary-light text-Cprimary-dark shadow-md transition-all hover:bg-Cprimary hover:text-Cprimary-light">
                    Ir para o chat
                </a>
            </div>

            @if(($chatUnread ?? 0) > 0)
                <div class="flex flex-col gap-[.4vmax]">
                    <div class="flex flex-row justify-between items-center">
                        <span class="text-[1vmax]">
                            {{ $chatLastSender ?? 'Nova mensagem' }}
                        </span>

                        <span class="text-[.8vmax] text-base-content/50">
                            {{ $chatLastTime ?? '' }}
                        </span>
                    </div>

                    <p class="text-[.9vmax] text-base-content truncate">
                        {{ $chatLastMessage ?? 'Você possui novas mensagens.' }}
                    </p>
                </div>
            @else
                <p class="text-[.95vmax] text-base-content/50">
                    Você não possui novas mensagens.
                </p>
            @endif

        </div>

    </div>
</div>


{{-- Relatórios --}}
<div class="card bg-base-100 shadow-md row-span-4 flex-col  pt-[1.5vmax] pl-[.7vmax] bg-Ccard">
    <div class="card-body flex flex-col items-start justify-start">

        <div class="flex flex-col gap-[.1vmax]">
            <div class="flex flex-row items-center">
                <span class="text-[2.2vmax] font-bold">
                    Relatórios
                </span>
                <span class="text-[3.5vmax] font-bold leading-0">
                </span>
            </div>

            <div>
                <p class="text-base-content text-[1.1vmax] ">
                    Consulte e crie relatórios rapidamente
                </p>
            </div>
        </div>

        <div class="flex flex-col 
        gap-[1vmax] items-start">

            {{-- Novo relatório --}}
            <a href=""
               class="flex items-center
                justify-between w-full 
                p-[1.3vmax] rounded-box bg-Cprimary-light text-Cprimary-dark shadow-md transition-all hover:bg-Cprimary hover:text-Cprimary-light">

                <div class="flex flex-col">
                    <span class="text-[1.1vmax] font-bold">
                        Novo relatório
                    </span>

                    <span class="text-[.85vmax] opacity-70">
                        Crie um relatório rapidamente
                    </span>
                </div>

                <span class="text-[1.5vmax]">
                    ＋
                </span>
            </a>


            {{-- Lista de relatórios --}}
            <div class="flex flex-col gap-[.5vmax] leading-none">

                <div class="flex flex-row justify-center items-center">
                    <h2 class="text-[1.2vmax] font-bold">
                        Relatórios recentes
                    </h2>

                    <span class="
                    text-[1vmax] text-base-content/50 
                    p-[.5vmax]">
                        {{ count($recentReports ?? []) }}
                    </span>
                </div>

                @forelse(($recentReports ?? []) as $report)

                    <a href="{{ $report->url ?? '' }}"
                       class="flex flex-row items-center justify-between w-full p-[.5vmax] rounded-box transition-all hover:bg-base-200">

                        <div class="flex flex-col min-w-0">
                            <span class="text-[.95vmax] font-semibold truncate">
                                {{ $report->title }}
                            </span>

                            <span class="text-[.75vmax] text-base-content/50">
                                {{ $report->created_at ?? '' }}
                            </span>
                        </div>

                        <span class="text-[1vmax]">
                            →
                        </span>

                    </a>

                @empty

                    <div class="flex flex-col
                    justify-start">

                        <p class="text-[.9vmax] text-base-content/50">
                            Nenhum relatório recente.
                        </p>
                    </div>

                @endforelse

            </div>


            {{-- Ir para relatórios --}}
            <a href=""
               class="badge badge-xl badge-soft text-[1vmax] shadow-md w-fit transition-all hover:bg-Cprimary hover:text-Cprimary-light">
                Ver todos os relatórios
            </a>

        </div>

    </div>
</div>


{{-- Cronogramas --}}
<div class="card bg-base-100 shadow-md row-span-2 flex-col p-[.5vmax] bg-Ccard">
    <div class="card-body flex flex-col
    pt-[2vmax]">
    
        <div class="flex flex-col">
            <div class="flex items-center">
                <span class="text-[2.2vmax] font-bold">
                    Cronogramas
                </span>

            </div>
            <div>
                <p class="text-base-content text-[1.1vmax] pt-[.5vmax]">
                    Consulte os cronogramas disponíveis
                </p>
            </div>
        </div>

        <div class="flex flex-row items-center  ">

            {{-- <div class="flex flex-col">
                <span class="text-[1.2vmax] font-bold">
                    Seus cronogramas
                </span>

                <span class="text-[.9vmax] text-base-content/50">
                    {{ $totalSchedules ?? 0 }} disponíveis
                </span>
            </div> --}}

            <a href=""
               class="text-[1vmax] badge badge-xl badge-soft bg-Cprimary-light text-Cprimary-dark shadow-md transition-all hover:bg-Cprimary hover:text-Cprimary-light">
                Ver cronogramas
            </a>

        </div>

    </div>
</div>


{{-- Fórum --}}
<div class="card bg-base-100 shadow-md row-span-2 col-span-2 flex-col p-[1vmax] bg-Ccard">
    <div class="card-body flex flex-col items-start justify-">

        <div class="flex flex-col">
            <div class="flex flex-row items-center">
                <span class="text-[2.2vmax] font-bold">
                    Fórum
                </span>

                <span class="text-[3.5vmax] font-bold leading-0">
                </span>
            </div>

            <div>
                <p class="text-base-content text-[1.1vmax]">
                    Acompanhe as últimas discussões
                </p>
            </div>
        </div>


        {{-- Artigo mais recente --}}
        @if(isset($latestForumPost))

            <div class="flex flex-row items-center justify-between w-full gap-[1vmax]">

                <div class="flex flex-col min-w-0">

                    <span class="text-[.8vmax] text-base-content/50">
                        Artigo mais recente
                    </span>

                    <h2 class="text-[1.3vmax] font-bold truncate">
                        {{ $latestForumPost->title }}
                    </h2>

                    <p class="text-[.9vmax] text-base-content line-clamp-2">
                        {{ $latestForumPost->excerpt ?? $latestForumPost->content ?? '' }}
                    </p>

                    <span class="text-[.75vmax] text-base-content/50 mt-[.3vmax]">
                        {{ $latestForumPost->created_at ?? '' }}
                    </span>

                </div>


                <a href="{{ $latestForumPost->url ?? '' }}"
                   class="text-[1vmax] badge badge-xl badge-soft shadow-md w-fit transition-all hover:bg-Cprimary hover:text-Cprimary-light whitespace-nowrap">
                    Ler artigo
                </a>

            </div>

        @else

            <div class="flex flex-row items-center justify-between w-full">

                <p class="text-[.95vmax] text-base-content/50">
                    Nenhum artigo publicado recentemente.
                </p>

            </div>

        @endif


        {{-- Ir para fórum --}}
        <a href=""
           class="text-[1vmax] badge badge-xl badge-soft shadow-md w-fit transition-all hover:bg-Cprimary hover:text-Cprimary-light">
            Ir para o fórum
        </a>

    </div>
</div>