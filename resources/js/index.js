import { mountCursor } from './sections/cursor.js';
import { mountHeader } from './sections/header.js';
import { mountHero } from './sections/hero.js';
import { mountAbout } from './sections/about.js';
import { mountProgram } from './sections/program.js';
import { mountValues } from './sections/values.js';
import { mountValuesLine } from './sections/values-line.js';

// Composition root wires ports; each component owns its state and DOM.
const cursor = mountCursor();
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
const about = mountAbout(document.querySelector('[data-about]'));
const program = mountProgram(document.querySelector('[data-program]'));
const values = mountValues(document.querySelector('[data-program-values]'));
const valuesLine = mountValuesLine(document.querySelector('[data-values]'));
window.addEventListener('pagehide', event => {
    hero.suspend();
    about.suspend();
    program.suspend();
    values.suspend();
    valuesLine.suspend();
    if (!event.persisted) about.dispose();
    if (!event.persisted) program.dispose();
    if (!event.persisted) values.dispose();
    if (!event.persisted) valuesLine.dispose();
    if (!event.persisted) { header.dispose(); hero.dispose(); cursor.dispose(); }
});
window.addEventListener('pageshow', () => { hero.resume(); about.resume(); program.resume(); values.resume(); valuesLine.resume(); });
