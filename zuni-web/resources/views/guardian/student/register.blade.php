<x-panel.guardian>
    <form method="POST" action="{{ route('guardian.student.store') }}" class="space-y-8 col-span-4">
        @csrf
    
        {{-- Dados do aluno --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
    
                <h2 class="card-title mx-auto">
                    Dados do Aluno
                </h2>
    
                <div class="grid grid-cols-4 md:grid-cols-4 gap-4 px-[10vmax]">
    
                    <div class="form-control col-span-4">
                        <label class="label">
                            <span class="label-text">Nome</span>
                        </label>
    
                        <input
                            type="text"
                            name="name"
                            value="Felipe Muniz"
                            class="input input-bordered w-full"
                            required
                        >
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Data de Nascimento</span>
                        </label>
    
                        <input
                            type="date"
                            name="birth_date"
                            value="2020-06-12"
                            class="input input-bordered"
                            required
                        >
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Sexo</span>
                        </label>
    
                        <select
                            name="gender"
                            class="select select-bordered"
                            required
                        >
                            {{-- <option value="">Selecione</option> --}}
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Graduação</span>
                        </label>
    
                        <select
                            name="class"
                            class="select select-bordered w-full"
                            required
                        >
                            <option value="">Todos</option>
                            <option value="1-jardim">1º Jardim</option>
                            <option value="2-jardim">2º Jardim</option>
                            <option value="1-ano">1º Ano</option>
                            <option value="2-ano">2º Ano</option>
                            <option value="3-ano">3º Ano</option>
                            <option value="4-ano">4º Ano</option>
                            <option value="5-ano">5º Ano</option>
                            <option value="6-ano">6º Ano</option>
                            <option value="7-ano">7º Ano</option>
                            <option value="8-ano">8º Ano</option>
                            <option value="9-ano">9º Ano</option>
                        </select>
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Idade</span>
                        </label>
    
                        <input
                            type="number"
                            name="age"
                            min="0"
                            value=6
                            class="input input-bordered"
                        >
                    </div>
    
                </div>
    
            </div>
        </div>
    
        {{-- Endereço --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
    
                <h2 class="card-title mx-auto">
                    Endereço
                </h2>
    
                <div class="grid grid-cols-5 gap-4 px-[10vmax]">
    
                    <div class="col-span-4 form-control">
                        <label class="label">
                            <span class="label-text">Rua</span>
                        </label>
    
                        <input
                            type="text"
                            name="street"
                            value="Rua Waldemar Lopes"
                            class="input input-bordered w-full"
                        >
                    </div>
    
                    <div class="col-span-1 form-control">
                        <label class="label">
                            <span class="label-text">Número</span>
                        </label>
    
                        <input
                            type="text"
                            name="number"
                            value="133"
                            class="input input-bordered"
                        >
                    </div>
    
                    <div class="col-span-2 form-control">
                        <label class="label">
                            <span class="label-text">Bairro</span>
                        </label>
    
                        <input
                            type="text"
                            name="district"
                            value="Vila Tupy"
                            class="input input-bordered"
                        >
                    </div>
    
                    <div class="col-span-2 form-control">
                        <label class="label">
                            <span class="label-text">Cidade</span>
                        </label>
    
                        <input
                            type="text"
                            name="city"
                            value="Registro"
                            class="input input-bordered"
                        >
                    </div>
    
                    <div class="col-span-1 form-control">
                        <label class="label">
                            <span class="label-text">Estado</span>
                        </label>
    
                        <input
                            type="text"
                            name="state"
                            maxlength="2"
                            value="SP"
                            class="input input-bordered"
                        >
                    </div>
    
                </div>
    
            </div>
        </div>
    
        {{-- Informações médicas --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
    
                <h2 class="card-title mx-auto">
                    Informações Médicas
                </h2>
    
                <div class="grid md:grid-cols-1 gap-4 mx-auto">
    
                    <label class="label cursor-pointer justify-flex-start">
                        <input
                            type="checkbox"
                            name="neurodivergent"
                            value="1"
                            class="toggle toggle-primary"
                        >
                        <span>Neurodivergencia</span>
    
                    </label>
    
                    <label class="label cursor-pointer justify-flex-start">
    
                        <input
                            type="checkbox"
                            name="allergy"
                            value="1"
                            class="toggle toggle-primary"
                        >
                        <span>Possui alergias</span>
                    </label>
    
                    <label class="label cursor-pointer justify-flex-start">
    
                        <input
                            type="checkbox"
                            name="food_restriction"
                            value="1"
                            class="toggle toggle-primary"
                        >
                        <span>Restrição alimentar</span>
                    </label>
    
                    <label class="label cursor-pointer justify-flex-start">
    
                        <input
                            type="checkbox"
                            name="special_care"
                            value="1"
                            class="toggle toggle-primary"
                        >
                        <span>Necessita cuidados especiais</span>
                    </label>
    
                </div>
    
                <div class="form-control mt-4 mx-auto">
                    <label class="label">
                        <span class="label-text">
                            Observações
                        </span>
                    </label>
    
                    <textarea
                        name="notes"
                        rows="5"
                        class="textarea textarea-bordered"
                    ></textarea>
                </div>
    
            </div>
        </div>
    
        <div class="flex justify-end">
            <button type="submit" class="py-2 px-2 rounded-full bg-Csecondary text-white font-medium hover:brightness-110 transition">
                Cadastrar Aluno
            </button>
        </div>
    
    </form>
</x-panel.guardian>