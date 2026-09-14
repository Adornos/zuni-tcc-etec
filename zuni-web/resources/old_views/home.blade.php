<x-layout>
    <x-slot:title> Home </x-slot:title>

    <section class="slogan h-[80vmin]">

        <div class="absolute z-10 flex flex-col gap-[1.2max] max-w-[28.8vw] text-Ctext left-[20vmax] top-[27vmin]">
    
            <h1 class="text-[3vmax] leading-[1.2em] font-bold ">
                Onde a Tecnologia Encontra a Educação
            </h1>

            <p class="text-[1.35vmax] leading-relaxed">
                Sistema de gestão escolar que conecta pais e professores.
            </p>

            <a
                href="#"
                class="w-fit text-[1.2vmax] leading-relaxed rounded-[0.9vmax] bg-Csecondary px-[1.8vmax] mt-[1.8vmax] py-[0.9vmax] text-white transition hover:bg-Csecondary-dark"
            >
                Sobre Nosso Serviço
            </a>

        </div>
        <img src="{{ asset('images/home/circle.svg') }}" alt="" class="circle">
        <img src="{{ asset('images/home/img-0.svg') }}" alt="" class="kid">

    </section>

    <section class="propose w-full py-16 px-6 bg-linear-to-b from-white to-Cprimary-light">
        <div class="max-w-6xl mx-auto">
            
            <h2 class="text-3xl md:text-4xl font-bold text-Ctext mb-10 text-center">
            Nosso Diferencial
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="text-center p-6 rounded-4xl bg-gray-50 shadow-[4px_4px_20px_rgba(0,0,0,0.3)]">
                <h3 class="text-xl font-semibold text-Ctext mb-3">
                Contato Direto com os professores
                </h3>
                <p class="text-Ctext leading-relaxed">
                No Zuni é possível os pais dos alunos mandarem mensagens diretamente com os professores em tempo real para tirar dúvidas, de forma fácil, simples e prática
                </p>
            </div>

            <div class="text-center p-6 rounded-4xl bg-gray-50 shadow-[4px_4px_20px_rgba(0,0,0,0.3)]">
                <h3 class="text-xl font-semibold text-Ctext mb-3">
                Sistema próprio pra cantina!
                </h3>
                <p class="text-Ctext leading-relaxed">
                As escolas que utilizam o nosso sistema possuem um próprio sistema para pagamento dos lanches, utilizando créditos digitais adicionados pelos pais dentro do próprio aplicativo Zuni, otimizando o tempo de lanche dos pequenos e facilitando a organização da cantina.
                </p>
            </div>

            <div class="text-center p-6 rounded-4xl bg-gray-50 shadow-[4px_4px_20px_rgba(0,0,0,0.3)]">
                <h3 class="text-xl font-semibold text-Ctext mb-3">
                Contato Direto com os professores
                </h3>
                <p class="text-Ctext leading-relaxed">
                No Zuni é possível os pais dos alunos mandarem mensagens diretamente com os professores em tempo real para tirar dúvidas, de forma fácil, simples e prática
                </p>
            </div>

            </div>
        </div>
    </section>

    <section class="w-full py-16 px-6 bg-linear-to-b from-Cprimary-light to-white">
    <div class="max-w-6xl mx-auto">
        
        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-12">

        <div class="space-y-6">

            <h2 class="text-3xl md:text-4xl font-bold text-Ctext leading-tight">
            ACOMPANHE SEU FILHO MESMO NO TRABALHO!
            </h2>

            <p class="text-Ctext text-lg font-light leading-relaxed">
            Quer saber se o seu filho comeu tudo, escovou os dentes ou até mesmo conseguiu realizar as atividades propostas na aula? <br><br>
            Instale o Zuni e acompanhe o dia do seu filho!
            </p>

            <div class="flex flex-col sm:flex-row gap-4 pt-2">

            <a href="#" 
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-Csecondary text-white font-semibold hover:bg-Csecondary-dark transition">
                Acesse no seu iPhone
                <span>→</span>
            </a>

            <a href="#" 
                class="inline-flex items-center justify-center px-6 py-3 rounded-full border-2 border-Cprimary text-Cprimary font-semibold bg-white hover:bg-Cprimary hover:text-white transition">
                Acesse no android
            </a>

            </div>

        </div>

        <div class="relative flex justify-center items-center">

            <div class="absolute bottom-0 w-[40vmin] h-[40vmin] bg-Csecondary">
            
            </div>
            <img src="{{ asset('images/home/img-1.svg') }}" 
                alt="Zuni app preview"
                class="relative bottom-0 z-10 max-w-[80vmin]">

        </div>

        </div>

    </div>
    </section>

    <section class="w-full py-16 px-6 bg-white">
        <div class="max-w-xl mx-auto">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            <div class="p-8 ">

                <h2 class="text-2xl md:text-xl font-bold text-Ctext mb-4">
                Pais de Aluno
                </h2>

                <p class="text-Ctext text-md leading-relaxed mb-6">
                Quer matricular seus filhos em alguma de nossas escolas que possuem o sistema Zuni? Veja as escolas próximas de sua casa.
                </p>

                <a href="#"
                class="text-sm inline-flex items-center justify-center px-6 py-3 rounded-full border-2 border-Cprimary text-Cprimary font-semibold bg-white hover:bg-Cprimary hover:text-white transition">
                Ver mais opções
                </a>

            </div>

            <div class="p-8 ">

                <h2 class="text-2xl md:text-xl font-bold text-Ctext mb-4">
                Professor
                </h2>

                <p class="text-Ctext text-md leading-relaxed mb-6">
                Deseja integrar uma das nossas escolas que possuem o sistema Zuni integrado no sistema de ensino? Mande seu currículo aqui.
                </p>

                <a href="#"
                class="text-sm inline-flex items-center justify-center px-6 py-3 rounded-full border-2 border-Cprimary text-Cprimary font-semibold bg-white hover:bg-Cprimary hover:text-white transition">
                Opções de contato
                </a>

            </div>

            </div>

        </div>
        </section>

    
</x-layout>