<!-- row-span-4 col-span-4 card 100% -->
<div class="card bg-base-100 shadow-md hidden row-span-4 col-span-4 flex-col p-[1vmax] bg-Ccard overflow-y-auto">

    <div class="card-body flex flex-col">
    <div class="flex flex-row items-center justify-between w-full">
        <div class="flex flex-row items-center">
            <span class="text-[2.2vmax] font-bold">
                1° ANO
            </span>
            <span class="text-[3.5vmax] font-bold leading-0">
            </span>
        </div>
        <div class="flex flex-row items-center">
            <span class="text-[2.2vmax] font-bold">
                2° ANO
            </span>
            <span class="text-[3.5vmax] font-bold leading-0">
            </span>
        </div>
        <div class="flex flex-row items-center">
            <span class="text-[2.2vmax] font-bold">
                3° ANO
            </span>
            <span class="text-[3.5vmax] font-bold leading-0">
            </span>
        </div>    
    </div>   
    
             <!-- Linha divisória -->
            <div class="mt-5 h-[.1vmax] bg-[#e8eef6]"></div>

            <!-- Lista -->
            <div class="mt-16 space-y-10">

                <!-- Aluno -->
                <div
                    class="flex h-[84px] items-center rounded-[25px] bg-[#f8fbff]
                           px-8 shadow-[0_5px_12px_rgba(90,110,140,0.10)]
                           transition hover:-translate-y-0.5 hover:shadow-md"
                >
                
                <!-- Implementar a imagem colocada na matrícula do aluno, caso não tenha imagem, colocar a imagem padrão do usuário. -->
                <div class="mr-7 h-[46px] w-[46px] rounded-full bg-[#d9d9d9]"></div>

                    <span class="text-[19px] font-medium text-[#222]">
                        Aderildo da Silva Oliveira
                    </span>

                </div>
            </div>

    </div>
</div>


<form class="card bg-base-100 shadow-xl p-6 max-w-md mx-auto row-span-2">


    <h2 class="card-title mb-4">Cadastro Escolar</h2>

    <div class="form-control w-full">
        <label class="label">
            <span class="label-text">Ano escolar</span>
        </label>
        <select
            class="select select-bordered w-full"
            name="ano"
            id="ano"
        >
            <option disabled selected>Selecione o ano</option>
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

    <div class="form-control w-full">
        <label class="label">
            <span class="label-text">Status</span>
        </label>
        <select
            class="select select-bordered w-full"
            name="status"
            id="status"
        >
            <option disabled selected>Selecione o status</option>
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    <div class="form-control w-full">
        <label class="label">
            <span class="label-text">Turma</span>
        </label>
        <select
            class="select select-bordered w-full"
            name="turma"
            id="turma"
        >
            <option disabled selected>Selecione a turma</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-4">
        Salvar
    </button>
</form>

<div class="enrollments">
    @foreach ($enrollments as $enrollment)
        <div class="enrollment card bg-base-100 shadow-md p-4 mb-4">
            <p>Aluno: {{ $enrollment->student_name }}</p>
            <p>Ano Escolar: {{ $enrollment->school_year }}</p>
            <p>Status: {{ $enrollment->status }}</p>
            <p>Turma: {{ $enrollment->class ?? 'Não especificada' }}</p>
            <a class="btn btn-sm btn-primary" href="{{ route('coordinator.enrollment.show', $enrollment->student_id) }}">Editar</a>
        </div>
    @endforeach
</div>

<script>
    document.querySelector("#ano").addEventListener("change", (event) => {
        console.log("Ano alterado:", event.target.value);
    });

    document.querySelector("#status").addEventListener("change", (event) => {
        console.log("Status alterado:", event.target.value);
    });

    document.querySelector("#turma").addEventListener("change", (event) => {
        console.log("Turma alterada:", event.target.value);
    });
</script>
