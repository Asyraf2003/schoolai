import { mountCursor } from './sections/cursor.js';
import { mountHeader } from './sections/header.js';

const main = document.querySelector('main');
const cursor = mountCursor();
const header = mountHeader(document.querySelector('[data-header]'), {
    requestAudio: () => {},
    heroBoundary: () => -1,
    modalChanged(open) {
        if (!main) return;
        main.inert = open;
        if (open) main.setAttribute('aria-hidden', 'true');
        else main.removeAttribute('aria-hidden');
    },
});

window.addEventListener('pagehide', event => {
    if (!event.persisted) {
        header.dispose();
        cursor.dispose();
    }
});
