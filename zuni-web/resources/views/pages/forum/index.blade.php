<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    <div class="col-span-4 row-span-4">
        
        <div class="card bg-base-200 shadow-xl h-full">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>
                        <h2 class="card-title text-2xl">
                            Fórum
                        </h2>

                        <p class="text-base-content/60">
                            Participe das discussões e compartilhe informações.
                        </p>
                    </div>

                    <button class="btn btn-primary">
                        + Nova discussão
                    </button>

                </div>


                {{-- BUSCA --}}
                <div class="form-control mt-4">
                    <label class="input input-bordered flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 opacity-50"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z"
                            />
                        </svg>

                        <input
                            type="text"
                            placeholder="Pesquisar no fórum..."
                            class="grow"
                        />
                    </label>
                </div>


                {{-- CATEGORIAS --}}
                <div class="flex flex-wrap gap-2 mt-2">

                    <button class="btn btn-sm btn-primary">
                        Todas
                    </button>

                    <button class="btn btn-sm btn-ghost">
                        Avisos
                    </button>

                    <button class="btn btn-sm btn-ghost">
                        Atividades
                    </button>

                    <button class="btn btn-sm btn-ghost">
                        Dúvidas
                    </button>

                    <button class="btn btn-sm btn-ghost">
                        Sugestões
                    </button>

                </div>


                {{-- DISCUSSÕES --}}
                <div class="space-y-3 mt-4">

                    {{-- DISCUSSÃO 1 --}}
                    <div class="card bg-base-100 shadow-sm border border-base-300">
                        <div class="card-body p-4">

                            <div class="flex items-start gap-3">

                                <div class="avatar placeholder">
                                    <div class="bg-primary text-primary-content rounded-full w-10">
                                        <span>MS</span>
                                    </div>
                                </div>

                                <div class="flex-1">

                                    <div class="flex justify-between gap-2">

                                        <div>
                                            <h3 class="font-semibold text-lg">
                                                Reunião de pais
                                            </h3>

                                            <p class="text-sm text-base-content/60">
                                                Maria Silva · há 2 horas
                                            </p>
                                        </div>

                                        <div class="badge badge-primary badge-outline">
                                            Avisos
                                        </div>

                                    </div>

                                    <p class="text-sm mt-2 text-base-content/80">
                                        Gostaria de saber se a reunião de pais desta
                                        semana será presencial ou online.
                                    </p>

                                    <div class="flex gap-4 mt-3 text-sm text-base-content/60">

                                        <span>
                                            💬 8 respostas
                                        </span>

                                        <span>
                                            👁 32 visualizações
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>


                    {{-- DISCUSSÃO 2 --}}
                    <div class="card bg-base-100 shadow-sm border border-base-300">
                        <div class="card-body p-4">

                            <div class="flex items-start gap-3">

                                <div class="avatar placeholder">
                                    <div class="bg-secondary text-secondary-content rounded-full w-10">
                                        <span>JO</span>
                                    </div>
                                </div>

                                <div class="flex-1">

                                    <div class="flex justify-between gap-2">

                                        <div>
                                            <h3 class="font-semibold text-lg">
                                                Atividade da próxima semana
                                            </h3>

                                            <p class="text-sm text-base-content/60">
                                                João Oliveira · ontem
                                            </p>
                                        </div>

                                        <div class="badge badge-secondary badge-outline">
                                            Atividades
                                        </div>

                                    </div>

                                    <p class="text-sm mt-2 text-base-content/80">
                                        Alguém sabe quais materiais serão necessários
                                        para a atividade de sexta-feira?
                                    </p>

                                    <div class="flex gap-4 mt-3 text-sm text-base-content/60">

                                        <span>
                                            💬 5 respostas
                                        </span>

                                        <span>
                                            👁 21 visualizações
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>


                    {{-- DISCUSSÃO 3 --}}
                    <div class="card bg-base-100 shadow-sm border border-base-300">
                        <div class="card-body p-4">

                            <div class="flex items-start gap-3">

                                <div class="avatar placeholder">
                                    <div class="bg-accent text-accent-content rounded-full w-10">
                                        <span>CA</span>
                                    </div>
                                </div>

                                <div class="flex-1">

                                    <div class="flex justify-between gap-2">

                                        <div>
                                            <h3 class="font-semibold text-lg">
                                                Sugestão para a turma
                                            </h3>

                                            <p class="text-sm text-base-content/60">
                                                Carlos Almeida · 2 dias atrás
                                            </p>
                                        </div>

                                        <div class="badge badge-accent badge-outline">
                                            Sugestões
                                        </div>

                                    </div>

                                    <p class="text-sm mt-2 text-base-content/80">
                                        Poderíamos organizar uma atividade coletiva
                                        para a próxima semana?
                                    </p>

                                    <div class="flex gap-4 mt-3 text-sm text-base-content/60">

                                        <span>
                                            💬 12 respostas
                                        </span>

                                        <span>
                                            👁 47 visualizações
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</x-dynamic-component>