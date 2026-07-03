import './bootstrap';

const welcomeCard = document.querySelector('[data-welcome-card]');
const welcomeNote = document.querySelector('[data-welcome-note]');

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
