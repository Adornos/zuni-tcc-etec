<form class="card bg-base-100 shadow-xl p-6 col-span-4">


    <h2 class="card-title mb-4">Cadastro Escolar</h2>

    <div class="form flex gap-[2vmax]">

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

    </div>

</form>

@foreach ($enrollments as $enrollment)

    <div class="card bg-base-100 shadow-xl p-[2vmax] col-span-4">
        <div class="card bg-base-100 shadow-xl p-[2vmax]">
            <p>Student ID: {{ $enrollment->student_id }}</p>
            <p>Student Name: {{ $enrollment->name }}</p>
            <p>Status: {{ $enrollment->status }}</p>
            <a href="{{ route('coordinator.enrollment.show', $enrollment->student_id) }}" class="btn btn-primary w-fit">Mais informações</a>
        </div>
    </div>
    
@endforeach