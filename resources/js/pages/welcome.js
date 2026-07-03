import { qs } from '../core/dom.js';

// Page module welcome: hanya animasi ringan untuk halaman utama.
export function mount() {
    const welcomeCard = qs('[data-welcome-card]');
    const welcomeNote = qs('[data-welcome-note]');

    if (welcomeCard) {
        requestAnimationFrame(() => {
            welcomeCard.classList.add('is-visible');
        });
    }

    if (welcomeNote) {
        window.setInterval(() => {
            welcomeNote.classList.toggle('is-highlighted');
        }, 1800);
    }
}
