import { mountHeader } from './sections/header.js';
import { mountHero } from './sections/hero.js';

// Composition root wires ports; each component owns its state and DOM.
let hero;
const main = document.querySelector('main');
const header = mountHeader(document.querySelector('[data-header]'), {
    requestAudio: () => hero?.toggleAudio(),
    heroBoundary: () => hero?.boundary() ?? null,
    modalChanged(open) {
        main.inert = open;
        if (open) main.setAttribute('aria-hidden', 'true');
        else main.removeAttribute('aria-hidden');
    },
});
hero = mountHero(document.querySelector('[data-hero]'), {
    audioChanged: status => header.setAudio(status),
});
window.addEventListener('pagehide', event => {
    hero.suspend();
    if (!event.persisted) { header.dispose(); hero.dispose(); }
});
window.addEventListener('pageshow', () => hero.resume());
