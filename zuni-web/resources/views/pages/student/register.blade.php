<x-dynamic-component :component="'panel.' . auth()->user()->role->value">
    <form method="POST" action="{{ route('guardian.student.store') }}" class="space-y-8 col-span-full">
        @csrf
    
        {{-- Dados do aluno --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4 sm:p-6">
    
                <h2 class="card-title mx-auto text-xl">
                    Dados do Aluno
                </h2>
    
                <div class="grid grid-cols-1 gap-4 px-2 sm:grid-cols-2 sm:px-4 lg:grid-cols-3 lg:px-8">
    
                    <div class="form-control col-span-full">
                        <label class="label">
                            <span class="label-text text-base font-medium">Nome</span>
                        </label>
    
                        <input
                            type="text"
                            name="name"
                            value="Felipe Muniz"
                            class="input input-bordered min-h-12 w-full text-base"
                            required
                        >
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-base font-medium">Data de Nascimento</span>
                        </label>
    
                        <input
                            type="date"
                            name="birth_date"
                            value="2020-06-12"
                            class="input input-bordered min-h-12 w-full text-base"
                            required
                        >
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-base font-medium">Sexo</span>
                        </label>
    
                        <select
                            name="gender"
                            class="select select-bordered min-h-12 w-full text-base"
                            required
                        >
                            {{-- <option value="">Selecione</option> --}}
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-base font-medium">Graduação</span>
                        </label>
    
                        <select
                            name="class"
                            class="select select-bordered min-h-12 w-full text-base"
                            required
                        >
                            @foreach(App\Enums\ClassroomGrade::cases() as $grade)
                                <option value="{{$grade}}">{{$grade->label()}}</option>
                            @endforeach
                        </select>
                    </div>
    
                </div>
    
            </div>
        </div>
    
        {{-- Endereço --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4 sm:p-6">
    
                <h2 class="card-title mx-auto text-xl">
                    Endereço
                </h2>
    
                <div class="grid grid-cols-1 gap-4 px-2 sm:grid-cols-2 sm:px-4 lg:grid-cols-5 lg:px-8">
    
                    <div class="form-control col-span-full lg:col-span-4">
                        <label class="label">
                            <span class="label-text text-base font-medium">Rua</span>
                        </label>
    
                        <input
                            type="text"
                            name="street"
                            value="Rua Waldemar Lopes"
                            class="input input-bordered min-h-12 w-full text-base"
                        >
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-base font-medium">Número</span>
                        </label>
    
                        <input
                            type="text"
                            name="number"
                            value="133"
                            class="input input-bordered min-h-12 w-full text-base"
                        >
                    </div>
    
                    <div class="form-control lg:col-span-2">
                        <label class="label">
                            <span class="label-text text-base font-medium">Bairro</span>
                        </label>
    
                        <input
                            type="text"
                            name="district"
                            value="Vila Tupy"
                            class="input input-bordered min-h-12 w-full text-base"
                        >
                    </div>
    
                    <div class="form-control lg:col-span-2">
                        <label class="label">
                            <span class="label-text text-base font-medium">Cidade</span>
                        </label>
    
                        <input
                            type="text"
                            name="city"
                            value="Registro"
                            class="input input-bordered min-h-12 w-full text-base"
                        >
                    </div>
    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-base font-medium">Estado</span>
                        </label>
    
                        <input
                            type="text"
                            name="state"
                            maxlength="2"
                            value="SP"
                            class="input input-bordered min-h-12 w-full text-base"
                        >
                    </div>
    
                </div>
    
            </div>
        </div>
    
        {{-- Informações médicas --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4 sm:p-6">
    
                <h2 class="card-title mx-auto text-xl">
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
                        <span class="text-base">Neurodivergencia</span>
    
                    </label>
    
                    <label class="label cursor-pointer justify-flex-start">
    
                        <input
                            type="checkbox"
                            name="allergy"
                            value="1"
                            class="toggle toggle-primary"
                        >
                        <span class="text-base">Possui alergias</span>
                    </label>
    
                    <label class="label cursor-pointer justify-flex-start">
    
                        <input
                            type="checkbox"
                            name="food_restriction"
                            value="1"
                            class="toggle toggle-primary"
                        >
                        <span class="text-base">Restrição alimentar</span>
                    </label>
    
                    <label class="label cursor-pointer justify-flex-start">
    
                        <input
                            type="checkbox"
                            name="special_care"
                            value="1"
                            class="toggle toggle-primary"
                        >
                        <span class="text-base">Necessita cuidados especiais</span>
                    </label>
    
                </div>
    
                <div class="form-control mt-4 w-full">
                    <label class="label">
                        <span class="label-text text-base font-medium">
                            Observações
                        </span>
                    </label>
    
                    <textarea
                        name="notes"
                        rows="5"
                        class="textarea textarea-bordered min-h-32 w-full text-base"
                    ></textarea>
                </div>
    
            </div>
        </div>
    
        <div class="flex flex-col sm:flex-row sm:justify-end">
            <button type="submit" class="w-full rounded-full bg-Csecondary px-5 py-3 text-base font-medium text-white transition hover:brightness-110 sm:w-auto">
                Cadastrar Aluno
            </button>
        </div>
    
    </form>
</x-dynamic-component>