import Alpine from 'alpinejs'
import mask from '@alpinejs/mask'

Alpine.plugin(mask)

window.Alpine = Alpine

Alpine.start()

const homePage = document.querySelector('.home-page')
const revealItems = homePage?.querySelectorAll('.home-reveal')

if (homePage && revealItems?.length && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible')
            } else {
                entry.target.classList.remove('is-visible')
            }
        })
    }, { threshold: 0.15 })

    homePage.classList.add('has-scroll-reveal')
    revealItems.forEach((item) => revealObserver.observe(item))
}
