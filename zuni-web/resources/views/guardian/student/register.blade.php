<form method="POST" action="{{ route('guardian.student.register') }}" class="space-y-8 col-span-4">
    @csrf

    {{-- Dados do aluno --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Dados do Aluno
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Nome</span>
                    </label>

                    <input
                        type="text"
                        name="name"
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
                        <option value="">Selecione</option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Turma</span>
                    </label>

                    <input
                        type="text"
                        name="class"
                        class="input input-bordered"
                    >
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Idade</span>
                    </label>

                    <input
                        type="number"
                        name="age"
                        min="0"
                        class="input input-bordered"
                    >
                </div>

            </div>

        </div>
    </div>

    {{-- Endereço --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Endereço
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                <div class="md:col-span-8 form-control">
                    <label class="label">
                        <span class="label-text">Rua</span>
                    </label>

                    <input
                        type="text"
                        name="street"
                        class="input input-bordered"
                    >
                </div>

                <div class="md:col-span-4 form-control">
                    <label class="label">
                        <span class="label-text">Número</span>
                    </label>

                    <input
                        type="text"
                        name="number"
                        class="input input-bordered"
                    >
                </div>

                <div class="md:col-span-4 form-control">
                    <label class="label">
                        <span class="label-text">Bairro</span>
                    </label>

                    <input
                        type="text"
                        name="district"
                        class="input input-bordered"
                    >
                </div>

                <div class="md:col-span-5 form-control">
                    <label class="label">
                        <span class="label-text">Cidade</span>
                    </label>

                    <input
                        type="text"
                        name="city"
                        class="input input-bordered"
                    >
                </div>

                <div class="md:col-span-3 form-control">
                    <label class="label">
                        <span class="label-text">Estado</span>
                    </label>

                    <input
                        type="text"
                        name="state"
                        maxlength="2"
                        class="input input-bordered"
                    >
                </div>

            </div>

        </div>
    </div>

    {{-- Informações médicas --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">

            <h2 class="card-title">
                Informações Médicas
            </h2>

            <div class="grid md:grid-cols-2 gap-4">

                <label class="label cursor-pointer justify-between">
                    <span>Neurodivergente</span>

                    <input
                        type="checkbox"
                        name="neurodivergent"
                        value="1"
                        class="toggle toggle-primary"
                    >
                </label>

                <label class="label cursor-pointer justify-between">
                    <span>Possui alergias</span>

                    <input
                        type="checkbox"
                        name="allergy"
                        value="1"
                        class="toggle toggle-primary"
                    >
                </label>

                <label class="label cursor-pointer justify-between">
                    <span>Restrição alimentar</span>

                    <input
                        type="checkbox"
                        name="food_restriction"
                        value="1"
                        class="toggle toggle-primary"
                    >
                </label>

                <label class="label cursor-pointer justify-between">
                    <span>Necessita cuidados especiais</span>

                    <input
                        type="checkbox"
                        name="special_care"
                        value="1"
                        class="toggle toggle-primary"
                    >
                </label>

            </div>

            <div class="form-control mt-4">
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
        <button type="submit" class="btn btn-primary">
            Cadastrar Aluno
        </button>
    </div>

</form>