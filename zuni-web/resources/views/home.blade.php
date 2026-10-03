<x-layout>
    <x-slot:title>Home</x-slot:title>

    <section class="slogan relative overflow-hidden py-12 px-6 sm:py-16 lg:h-[80vmin] lg:min-h-0 lg:px-0 lg:py-0 bg-gradient-to-b from-Cprimary-light via-white to-white">

        <div class="relative z-10 mx-auto flex w-full max-w-[500px] flex-col gap-5 text-Ctext
            lg:absolute lg:left-[19vmax] lg:top-[20vmin] lg:mx-0 lg:w-auto lg:max-w-[28.8vw] lg:gap-[1.2max]">

            <h1 class="text-4xl leading-tight font-bold w-[35vmax] sm:text-5xl lg:text-[3.5vmax] lg:leading-[1.2em]">
                Onde a Tecnologia Encontra a Educação
            </h1>

            <p class="text-lg leading-relaxed sm:text-xl lg:text-[1.35vmax]">
                Sistema de gestão escolar que conecta pais e professores.
            </p>

            <a
                href="#"
                class="w-fit rounded-xl bg-Csecondary px-6 py-3 text-base leading-relaxed text-white transition hover:bg-Csecondary-dark sm:px-7 sm:py-3.5 lg:mt-[1.8vmax] lg:rounded-[0.9vmax] lg:px-[1.8vmax] lg:py-[0.9vmax] lg:text-[1.2vmax]"
            >
                Sobre Nosso Serviço
            </a>

        </div>

        <img
            src="{{ asset('images/home/circle.svg') }}"
            alt=""
            class="circle absolute hidden lg:right-[-5vw] lg:top-0 lg:block lg:w-[55vmax]"
        >

        <img
            src="{{ asset('images/home/img-0.svg') }}"
            alt="criança escolar"
            class="kid absolute z-10 hidden lg:bottom-0 lg:right-[8vw] lg:block lg:w-[40vmax] lg:max-w-none "
        >

    </section>

    <section class="features relative isolate overflow-hidden w-full py-30 px-6
                    before:content-[''] before:absolute before:-z-10
                    before:left-1/2 before:-translate-x-1/2
                    before:top-[calc(100%-30rem)]
                    before:w-[150vw] before:aspect-square before:rounded-full
                    before:bg-linear-to-b before:from-white before:via-Cprimary-light before:via-12% before:to-Cprimary-light">
    <div class="max-w-6xl mx-auto">

        @php
            $features = [
                [
                    'img'   => 'images/home/img-3.png',
                    'alt'   => 'Criança sorrindo com mochila',
                    'title' => 'Controle Completo',
                    'text'  => 'Acompanhe o desempenho, a frequência e as principais atividades do seu filho de maneira simples e acessível.',
                ],
                [
                    'img'   => 'images/home/img-4.png',
                    'alt'   => 'Família sorrindo junta',
                    'title' => 'Gestão Simplificada',
                    'text'  => 'Tenha todas as informações escolares organizadas em um só lugar, facilitando a rotina e o gerenciamento da escola.',
                ],
                [
                    'img'   => 'images/home/img-5.png',
                    'alt'   => 'Professora sorrindo',
                    'title' => 'Contato Integrado',
                    'text'  => 'Mantenha uma comunicação mais rápida e eficiente entre responsáveis, professores e toda a comunidade escolar.',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8 lg:gap-14">

            @foreach ($features as $feature)
                <div class="flex flex-col items-center md:items-start max-w-sm mx-auto md:mx-0 pb-[2vmax]">

                    <div class="w-48 h-48 lg:w-56 lg:h-56 rounded-full bg-Csecondary overflow-hidden mb-8 self-center">
                        <img
                            src="{{ asset($feature['img']) }}"
                            alt="{{ $feature['alt'] }}"
                            class="w-full h-full object-cover object-top"
                        >
                    </div>

                    <h3 class="text-2xl lg:text-3xl font-bold uppercase text-Ctext mb-4 text-center md:text-left">
                        {{ $feature['title'] }}
                    </h3>

                    <p class="text-Ctext text-base leading-relaxed text-justify">
                        {{ $feature['text'] }}
                    </p>

                </div>
            @endforeach

        </div>
    </div>
</section>

    <section class="w-full pt-16 pb-4 px-6 py-30 bg-linear-to-b from-Cprimary-light to-white md:py-16">
        <div class="max-w-6xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-x-12 gap-y-6">

                <div class="order-1 space-y-6 md:col-start-1 md:row-start-1">

                    <h2 class="text-3xl md:text-4xl font-bold text-Ctext leading-tight">
                        ACOMPANHE SEU FILHO MESMO NO TRABALHO!
                    </h2>

                    <p class="text-Ctext text-lg font-light leading-relaxed">
                        Quer saber se o seu filho comeu tudo, escovou os dentes ou até mesmo conseguiu realizar as atividades propostas na aula? <br><br>
                        Instale o Zuni e acompanhe o dia do seu filho!
                    </p>

                </div>

                <div class="order-3 flex flex-col sm:flex-row gap-4 pt-2 md:col-start-1 md:row-start-2">

                    <a
                        href="#"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-Csecondary text-white font-semibold hover:bg-Csecondary-dark transition"
                    >
                        Acesse no seu iPhone
                        <span>→</span>
                    </a>

                    <a
                        href="#"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-full border-2 border-Cprimary text-Cprimary font-semibold bg-white hover:bg-Cprimary hover:text-white transition"
                    >
                        Acesse no android
                    </a>

                </div>

                <div class="order-2 relative flex justify-center items-center md:col-start-2 md:row-start-1 md:row-span-2">

                    <div class="absolute bottom-0 w-[40vmin] h-[40vmin] bg-Csecondary">
                    </div>

                    <img
                        src="{{ asset('images/home/img-1.svg') }}"
                        alt="Zuni app preview"
                        class="relative bottom-0 z-10 max-w-[65vmin] md:max-w-[80vmin]"
                    >

                </div>

            </div>

        </div>
    </section>

    <section class="w-full py-16 px-6 bg-white">
        <div class="max-w-xl mx-auto">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                <div class="p-8">

                    <h2 class="text-2xl md:text-xl font-bold text-Ctext mb-4">
                        Pais de Aluno
                    </h2>

                    <p class="text-Ctext text-md leading-relaxed mb-6">
                        Quer matricular seus filhos em alguma de nossas escolas que possuem o sistema Zuni? Veja as escolas próximas de sua casa.
                    </p>

                    <a
                        href="#"
                        class="text-sm inline-flex items-center justify-center px-6 py-3 rounded-full border-2 border-Cprimary text-Cprimary font-semibold bg-white hover:bg-Cprimary hover:text-white transition"
                    >
                        Ver mais opções
                    </a>

                </div>

                <div class="p-8">

                    <h2 class="text-2xl md:text-xl font-bold text-Ctext mb-4">
                        Professor
                    </h2>

                    <p class="text-Ctext text-md leading-relaxed mb-6">
                        Deseja integrar uma das nossas escolas que possuem o sistema Zuni integrado no sistema de ensino? Mande seu currículo aqui.
                    </p>

                    <a
                        href="#"
                        class="text-sm inline-flex items-center justify-center px-6 py-3 rounded-full border-2 border-Cprimary text-Cprimary font-semibold bg-white hover:bg-Cprimary hover:text-white transition"
                    >
                        Opções de contato
                    </a>

                </div>

            </div>

        </div>
    </section>

</x-layout>
