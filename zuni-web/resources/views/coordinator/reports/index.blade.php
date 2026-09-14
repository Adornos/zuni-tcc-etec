<x-panel.coordinator>

    <div class="col-span-4 row-span-4 bg-white shadow-md rounded-lg p-4">
    
        <h2 class="text-xl font-semibold mb-4 text-[2vmax] text-center">RELATÓRIO</h2>
    
        <form class=" p-4 mb-4">
        
        <!-- Título -->
    
            <div class="mb-4">
                <label for="titulo" class="block text-sm text-gray-600 mb-1">
                    Título
                </label>
    
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    placeholder="Digite o título do relatório"
                    class="w-full p-3 border border-gray-300 rounded-md focus:border-red-500 focus:outline-none"
                >
            </div>
            <!-- Referencia -->
            <div class="mb-4">
                <label for="index" class="block text-sm font-medium text-gray-600 mb-1">
                    Referência
                </label>
    
                <select
                    id="index"
                    name="index"
                    class="w-full border border-gray-300 rounded-md p-2 focus:border-red-500 focus:outline-none text-Cprimary/50"
                >
                    <option value="" disabled selected>Selecione uma referência</option>
                    <option value="1">Aluno</option>
                    <option value="2">Sala</option>
                    <option value="3">Geral</option>
                </select>

                
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
                    class="w-full text-gray-700 border border-gray-300 rounded-md p-2 focus:border-red-500 focus:outline-none"
                    placeholder="Digite o corpo do relatório"
                ></textarea>
            </div>
    
        <div class="mb-10">   
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
            
            <button
                type="submit"
                class="mt-4 bg-Csecondary text-white px-4 py-2 rounded-md hover:bg-red-700"
            >
                Salvar
            </button>
        </form>

       
          
       </div>
    
    </div>




    <script>
        document.getElementById('index').addEventListener('change', async function () {
            const index = this.value;
    
            if (!index) {
                return;
            }
    
            try {
                const response = await fetch(`/broadcast/data/${index}`);
    
                if (!response.ok) {
                    throw new Error('Erro ao buscar os dados.');
                }
    
                const result = await response.json();
    
                console.log(result);
    
                // Aqui você atualiza a interface
                // com result.data
    
            } catch (error) {
                console.error(error);
            }
        });
    </script>
</x-panel.coordinator>
