    @props(['profile'])
    
    <div class="grid grid-cols-4 grid-rows-4 gap-4 h-full">

        <!-- Perfil -->
        <div class="card bg-base-100 shadow-md col-span-4 row-span-4">

            <div class="card-body">

                <div class="flex items-center gap-6 mb-6">

                    <div class="avatar">
                        <div class="w-24 rounded-full">
                            <img src="https://ui-avatars.com/api/?name=João+Silva" />
                        </div>
                    </div>

                    <div>

                        <h2 class="card-title text-3xl">
                            João da Silva
                        </h2>

                        <p class="text-base-content/60">
                            Responsável
                        </p>

                    </div>

                </div>

                <div class="divider">
                    Informações Pessoais
                </div>

                <div class="grid grid-cols-2 gap-6">

                    <!-- Nome -->
                    <label class="floating-label">
                        <input
                            type="text"
                            class="input input-bordered w-full"
                            value="João da Silva"
                        >
                        <span>Nome</span>
                    </label>

                    <!-- CPF -->
                    <label class="floating-label">
                        <input
                            type="text"
                            class="input input-bordered w-full"
                            value="123.456.789-00"
                        >
                        <span>CPF</span>
                    </label>

                    <!-- Email -->
                    <label class="floating-label">
                        <input
                            type="email"
                            class="input input-bordered w-full"
                            value="joao@email.com"
                        >
                        <span>Email</span>
                    </label>

                    <!-- Telefone -->
                    <label class="floating-label">
                        <input
                            type="text"
                            class="input input-bordered w-full"
                            value="(13) 99999-9999"
                        >
                        <span>Telefone</span>
                    </label>

                </div>

                <div class="divider mt-8">
                    Segurança
                </div>

                <div class="grid grid-cols-2 gap-6">

                    <label class="floating-label">
                        <input
                            type="password"
                            class="input input-bordered w-full"
                            placeholder="••••••••"
                        >
                        <span>Nova Senha</span>
                    </label>

                    <label class="floating-label">
                        <input
                            type="password"
                            class="input input-bordered w-full"
                            placeholder="••••••••"
                        >
                        <span>Confirmar Senha</span>
                    </label>

                </div>

                <div class="card-actions justify-end mt-8">

                    <button class="btn btn-outline">
                        Cancelar
                    </button>

                    <button class="btn btn-primary">
                        Salvar Alterações
                    </button>

                </div>

            </div>

        </div>

    </div>