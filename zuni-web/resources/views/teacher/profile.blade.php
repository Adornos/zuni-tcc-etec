<x-panel.teacher>
    @props(['profile'])
    
    
    <!-- Perfil -->
    <div class="card bg-base-100 shadow-md col-span-4 row-span-4">
    
        <div class="card-body">
    
            <div class="flex items-center gap-6 mb-6">
    
                <div class="avatar">
                    <div class="w-24 rounded-full">
                        <img src="https://ui-avatars.com/api/?name={{ $profile->name[0] ?? 'Sem nome' }}" />
                    </div>
                </div>
    
                <div>
    
                    <h2 class="card-title text-3xl">
                       {{ $profile->name ?? 'Sem nome' }}
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
                    <input type="text" class="input input-bordered w-full" value="{{ $profile->name ?? 'Sem nome' }}">
                    <span>Nome</span>
                </label>
    
                <!-- CPF -->
                <label class="floating-label">
                    <input type="text" class="input input-bordered w-full" value="{{ $profile->cpf ?? 'Sem cpf' }}">
                    <span>CPF</span>
                </label>
    
                <!-- Email -->
                <label class="floating-label">
                    <input type="email" class="input input-bordered w-full" value="{{ $profile->email ?? 'Sem email' }}">
                    <span>Email</span>
                </label>
    
                <!-- Telefone -->
                <label class="floating-label">
                    <input type="text" class="input input-bordered w-full" value="{{ $profile->phone ?? 'Sem telefone' }}">
                    <span>Telefone</span>
                </label>
    
            </div>
    
            <div class="divider mt-8">
                Segurança
            </div>
    
            <div class="grid grid-cols-2 gap-6">
    
                <label class="floating-label">
                    <input type="password" class="input input-bordered w-full" placeholder="••••••••">
                    <span>Nova Senha</span>
                </label>
    
                <label class="floating-label">
                    <input type="password" class="input input-bordered w-full" placeholder="••••••••">
                    <span>Confirmar Senha</span>
                </label>
    
            </div>
    
            <div class="card-actions justify-end mt-8">
    
                <button class="px-5 py-2 rounded-full border-2 border-Cprimary text-Cprimary font-medium hover:bg-Cprimary hover:text-white transition">
                    Cancelar
                </button>
    
                <button class="px-5 py-2 rounded-full bg-Csecondary text-white font-medium hover:bg-Csecondary-dark transition">
                    Salvar Alterações
                </button>
    
            </div>
    
        </div>
    
    </div>
</x-panel.teacher>