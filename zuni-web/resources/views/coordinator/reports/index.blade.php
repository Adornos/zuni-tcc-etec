<div class="col-span-2 row-span-4 bg-white shadow-md rounded-lg p-4">

    <h2 class="text-xl font-semibold mb-4 text-[2vmax] text-center">Edição/Criação de relatório</h2>

    <form class="bg-white shadow-md rounded-lg p-4 mb-4">

        <!-- Aluno -->
        <div class="mb-4">
            <label for="aluno" class="block text-sm font-medium text-gray-600 mb-1">
                Referência
            </label>

            <select
                id="aluno"
                name="aluno"
                class="w-full border border-gray-300 rounded-md p-2 focus:border-blue-500 focus:outline-none"
            >
                <option value="">Selecione uma referência</option>
                <option value="1">Aluno</option>
                <option value="2">Sala</option>
                <option value="3">Escola</option>
            </select>
        </div>

        <!-- Título -->
        <div class="mb-4">
            <label for="titulo" class="block text-sm text-gray-600 mb-1">
                Título
            </label>

            <input
                type="text"
                id="titulo"
                name="titulo"
                value="Titulo"
                class="w-full text-[2vmax] font-semibold p-3 border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none"
            >
        </div>

        <!-- Corpo -->
        <div class="mb-4">
            <label for="corpo" class="block text-sm font-medium text-gray-600 mb-1">
                Relatório
            </label>

            <textarea
                id="corpo"
                name="corpo"
                rows="4"
                class="w-full text-gray-700 border border-gray-300 rounded-md p-2 focus:border-blue-500 focus:outline-none"
            >Corpo</textarea>
        </div>

        <button
            type="submit"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
        >
            Salvar
        </button>

    </form>

</div>

<div class="col-span-2 row-span-4 bg-white shadow-md rounded-lg p-4">

    <h2 class="text-xl font-semibold mb-4 text-[2vmax] text-center">Aluno Relatado</h2>
        
            <div class="mb-4 p-4 border border-gray-300 rounded-md flex gap-[2vmax] items-center">
                <form action="get">
                    <input type="checkbox" name="selection" id="123" class="w-[1vmax] h-[1vmax]">
                </form>
                <div class="info">
                    <h3 class="text-lg font-semibold mb-2 text-[1vmax]">Luiz Felipe</h3>
                    <p class="text-gray-700 mb-2 text-[.75vmax]">Aluno</p>
                    <p class="text-gray-700 mb-2 text-[.75vmax]">Classe: 1º Ano</p>
                </div>
            </div>

   
</div>