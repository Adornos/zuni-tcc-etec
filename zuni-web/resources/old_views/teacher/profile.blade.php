<x-panel.teacher>
    @props(['profile'])
    
    <form action="{{ route('teacher.profile.save') }}" method="post" class="card bg-base-100 shadow-md col-span-4 row-span-4">
        @method('put')
        @csrf

    <!-- Perfil -->
    <div >
    
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
                    <input name="name" type="text" class="input input-bordered w-full"
                        value="{{ old('name', $profile->name) }}" required>
                    <span>Nome</span>
                </label>

                <!-- E-mail -->
                <label class="floating-label">
                    <input name="email" type="email" class="input input-bordered w-full"
                        value="{{ old('email', $profile->email) }}" required>
                    <span>E-mail</span>
                </label>

                <!-- CPF -->
                <label class="floating-label">
                    <input name="cpf" type="text" class="input input-bordered w-full"
                        value="{{ old('cpf', $profile->cpf) }}">
                    <span>CPF</span>
                </label>

                <!-- RG -->
                <label class="floating-label">
                    <input name="rg" type="text" class="input input-bordered w-full"
                        value="{{ old('rg', $profile->rg) }}">
                    <span>RG</span>
                </label>

                <!-- Telefone -->
                <label class="floating-label">
                    <input name="phone" type="text" class="input input-bordered w-full"
                        value="{{ old('phone', $profile->phone) }}">
                    <span>Telefone</span>
                </label>

                <!-- Data de nascimento -->
                <label class="floating-label">
                    <input name="birth_date" type="date" class="input input-bordered w-full"
                        value="{{ \Carbon\Carbon::parse($profile->birth_date)->format('Y-m-d') }}">
                    <span>Data de nascimento</span>
                </label>

                <!-- Gênero -->
                <label class="floating-label">
                    <select name="gender" class="input input-bordered w-full">
                        <option value="">Selecione</option>
                        <option value="M" @selected(old('gender', $profile->gender) === 'M')>Masculino</option>
                        <option value="F" @selected(old('gender', $profile->gender) === 'F')>Feminino</option>
                        <option value="O" @selected(old('gender', $profile->gender) === 'O')>Outro</option>
                    </select>
                    <span>Gênero</span>
                </label>

                <!-- Rua -->
                <label class="floating-label">
                    <input name="street" type="text" class="input input-bordered w-full"
                        value="{{ old('street', $profile->street) }}">
                    <span>Rua</span>
                </label>

                <!-- Número -->
                <label class="floating-label">
                    <input name="number" type="text" class="input input-bordered w-full"
                        value="{{ old('number', $profile->number) }}">
                    <span>Número</span>
                </label>

                <!-- Bairro -->
                <label class="floating-label">
                    <input name="district" type="text" class="input input-bordered w-full"
                        value="{{ old('district', $profile->district) }}">
                    <span>Bairro</span>
                </label>

                <!-- Cidade -->
                <label class="floating-label">
                    <input name="city" type="text" class="input input-bordered w-full"
                        value="{{ old('city', $profile->city) }}">
                    <span>Cidade</span>
                </label>

                <!-- Estado -->
                <label class="floating-label">
                    <input name="state" type="text" class="input input-bordered w-full"
                        value="{{ old('state', $profile->state) }}">
                    <span>Estado</span>
                </label>
                
            </div>
    
            <div class="divider mt-8">
                Segurança
            </div>
    
            <div class="grid grid-cols-2 gap-6">
    
                <label class="floating-label">
                    <input name='password' type="password" class="input input-bordered w-full" placeholder="••••••••">
                    <span>Nova Senha</span>
                </label>
    
                <label class="floating-label">
                    <input name='password_confirmation' type="password" class="input input-bordered w-full" placeholder="••••••••">
                    <span>Confirmar Senha</span>
                </label>
    
            </div>

            <!-- NÃO RETIRAR -->
            <label class="floating-label hidden">
                <select name="role" class="input input-bordered w-full">
                    <option value="{{$profile->role->value}}">{{$profile->role->label()}}</option>
                </select>
                <span>Função</span>
            </label>


            <div class="card-actions justify-end mt-8">
    
                <button class="px-5 py-2 rounded-full border-2 border-Cprimary text-Cprimary font-medium hover:bg-Cprimary hover:text-white transition" >
                    Cancelar
                </button>
    
                <button class="px-5 py-2 rounded-full bg-Csecondary text-white font-medium hover:bg-Csecondary-dark transition" type="submit">
                    Salvar Alterações
                </button>
    
            </div>
    
        </div>
    
    </div>

    </form>
</x-panel.teacher>