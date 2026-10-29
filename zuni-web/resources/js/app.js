import mask from '@alpinejs/mask'

// Páginas com Livewire: o Livewire já traz (e inicia) o próprio Alpine, que é o
// único que conhece magics como $wire. Só registramos o plugin nele.
document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(mask)
})

// Páginas sem nenhum componente Livewire: inicia o Alpine do projeto.
// A checagem usa o DOM (wire:id), que não depende da ordem de carregamento dos scripts.
document.addEventListener('DOMContentLoaded', async () => {
    if (document.querySelector('[wire\\:id]') || window.Livewire || window.Alpine) {
        return
    }

    const { default: Alpine } = await import('alpinejs')

    Alpine.plugin(mask)
    window.Alpine = Alpine
    Alpine.start()
})
