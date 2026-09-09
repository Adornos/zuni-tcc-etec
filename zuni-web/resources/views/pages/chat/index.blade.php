<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    <div class="col-span-4 row-span-4">

        <div class="card bg-base-200 shadow-xl h-full overflow-hidden">

            <div class="card-body p-0">

                <div class="flex h-full min-h-[600px]">

                    {{-- LISTA DE CONVERSAS --}}
                    <div class="w-1/3 border-r border-base-300 bg-base-100">

                        {{-- HEADER --}}
                        <div class="p-4 border-b border-base-300">

                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold">
                                    Mensagens
                                </h2>

                                <button class="btn btn-primary btn-sm">
                                    + Nova
                                </button>
                            </div>

                            {{-- BUSCA --}}
                            <label class="input input-sm input-bordered flex items-center gap-2 mt-3">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 opacity-50"
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
                                    placeholder="Pesquisar..."
                                    class="grow"
                                />

                            </label>

                        </div>


                        {{-- CONVERSAS --}}
                        <div class="overflow-y-auto">

                            {{-- CONVERSA ATIVA --}}
                            <div class="p-4 bg-base-200 cursor-pointer border-b border-base-300">

                                <div class="flex gap-3">

                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-primary-content rounded-full w-11">
                                            <span>MS</span>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">

                                        <div class="flex justify-between">
                                            <span class="font-semibold">
                                                Maria Silva
                                            </span>

                                            <span class="text-xs text-base-content/50">
                                                14:32
                                            </span>
                                        </div>

                                        <p class="text-sm text-base-content/60 truncate">
                                            Tudo bem, professora?
                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- CONVERSA --}}
                            <div class="p-4 hover:bg-base-200 cursor-pointer border-b border-base-300">

                                <div class="flex gap-3">

                                    <div class="avatar placeholder">
                                        <div class="bg-secondary text-secondary-content rounded-full w-11">
                                            <span>JO</span>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">

                                        <div class="flex justify-between">
                                            <span class="font-semibold">
                                                João Oliveira
                                            </span>

                                            <span class="text-xs text-base-content/50">
                                                12:10
                                            </span>
                                        </div>

                                        <p class="text-sm text-base-content/60 truncate">
                                            Obrigado pela informação!
                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- CONVERSA --}}
                            <div class="p-4 hover:bg-base-200 cursor-pointer border-b border-base-300">

                                <div class="flex gap-3">

                                    <div class="avatar placeholder">
                                        <div class="bg-accent text-accent-content rounded-full w-11">
                                            <span>CA</span>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">

                                        <div class="flex justify-between">
                                            <span class="font-semibold">
                                                Carlos Almeida
                                            </span>

                                            <span class="text-xs text-base-content/50">
                                                Ontem
                                            </span>
                                        </div>

                                        <p class="text-sm text-base-content/60 truncate">
                                            Podemos conversar sobre a reunião?
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- CHAT --}}
                    <div class="flex flex-col flex-1 bg-base-200">

                        {{-- HEADER DO CHAT --}}
                        <div class="flex items-center gap-3 p-4 bg-base-100 border-b border-base-300">

                            <div class="avatar placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-11">
                                    <span>MS</span>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-semibold">
                                    Maria Silva
                                </h3>

                                <p class="text-xs text-success">
                                    ● Online
                                </p>
                            </div>

                            <div class="ml-auto">

                                <button class="btn btn-ghost btn-sm">
                                    ⋮
                                </button>

                            </div>

                        </div>


                        {{-- MENSAGENS --}}
                        <div class="flex-1 overflow-y-auto p-6 space-y-4">

                            {{-- DATA --}}
                            <div class="text-center">
                                <span class="text-xs text-base-content/50">
                                    Hoje
                                </span>
                            </div>


                            {{-- MENSAGEM RECEBIDA --}}
                            <div class="chat chat-start">

                                <div class="chat-header text-xs text-base-content/50 mb-1">
                                    Maria
                                </div>

                                <div class="chat-bubble bg-base-100">
                                    Olá, professora! Tudo bem?
                                </div>

                                <div class="chat-footer text-xs opacity-50">
                                    14:28
                                </div>

                            </div>


                            {{-- MENSAGEM ENVIADA --}}
                            <div class="chat chat-end">

                                <div class="chat-bubble chat-bubble-primary">
                                    Olá, Maria! Tudo bem sim. Como posso ajudar?
                                </div>

                                <div class="chat-footer text-xs opacity-50">
                                    14:29 ✓✓
                                </div>

                            </div>


                            {{-- MENSAGEM RECEBIDA --}}
                            <div class="chat chat-start">

                                <div class="chat-header text-xs text-base-content/50 mb-1">
                                    Maria
                                </div>

                                <div class="chat-bubble bg-base-100">
                                    Gostaria de saber como foi o dia do João.
                                </div>

                                <div class="chat-footer text-xs opacity-50">
                                    14:31
                                </div>

                            </div>


                            {{-- MENSAGEM ENVIADA --}}
                            <div class="chat chat-end">

                                <div class="chat-bubble chat-bubble-primary">
                                    Ele participou bastante das atividades hoje!
                                    Também ajudou os colegas durante a brincadeira.
                                </div>

                                <div class="chat-footer text-xs opacity-50">
                                    14:32 ✓✓
                                </div>

                            </div>


                            {{-- MENSAGEM RECEBIDA --}}
                            <div class="chat chat-start">

                                <div class="chat-bubble bg-base-100">
                                    Que ótimo! Muito obrigada pela informação 😊
                                </div>

                                <div class="chat-footer text-xs opacity-50">
                                    14:32
                                </div>

                            </div>

                        </div>


                        {{-- CAMPO DE MENSAGEM --}}
                        <div class="p-4 bg-base-100 border-t border-base-300">

                            <form class="flex items-center gap-2">

                                <button
                                    type="button"
                                    class="btn btn-ghost btn-circle"
                                >
                                    📎
                                </button>

                                <input
                                    type="text"
                                    placeholder="Digite uma mensagem..."
                                    class="input input-bordered flex-1"
                                />

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Enviar
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-dynamic-component>