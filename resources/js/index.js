import { mountTestimonials } from './sections/testimonials.js';
import { mountStoriesMedia } from './sections/stories-media.js';
import { mountFooterHover } from './sections/footer-hover.js';
import { mountGallery } from './sections/gallery.js';
import { mountScrollPacing } from './sections/scroll-pacing.js';
import { mountScrollIndicator } from './sections/scroll-indicator.js';
import { mountCursor } from './sections/cursor.js';
import { mountHeader } from './sections/header.js';
import { mountHero } from './sections/hero.js';
import { mountAbout } from './sections/about.js';
import { mountProgram } from './sections/program.js';
import { mountValues } from './sections/values.js';
import { mountValuesLine } from './sections/values-line.js';
import { mountValuesCards } from './sections/values-cards.js';
import { mountSectionHeading } from './sections/section-heading.js';

// Composition root wires ports; each component owns its state and DOM.
const cursor = mountCursor();
const scrollIndicator = mountScrollIndicator(document.querySelector('[data-scroll-indicator]'));
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
const valuesCards = mountValuesCards(document.querySelector('[data-values]'));
const gallery = mountGallery(document.querySelector('[data-gallery]'));
const testimonials = mountTestimonials(document.querySelector('[data-testimonials]'));
const storiesHeading = mountSectionHeading(document.querySelector('[data-stories]'), 'stories', 900, 116);
const storiesMedia = mountStoriesMedia(document.querySelector('[data-stories]'));
const footerHover = mountFooterHover(document.querySelector('[data-footer-v2]'));
const scrollPacing = mountScrollPacing(main, { scrollChanged: () => testimonials.synchronize() });
window.addEventListener('pagehide', event => {
    gallery.suspend();
    testimonials.suspend();
    storiesHeading.suspend();
    storiesMedia.suspend();
    footerHover.suspend();
    scrollIndicator.suspend();
    scrollPacing.suspend();
    if (!event.persisted) { gallery.dispose(); testimonials.dispose(); storiesHeading.dispose(); storiesMedia.dispose(); footerHover.dispose(); }
    hero.suspend();
    about.suspend();
    program.suspend();
    values.suspend();
    valuesLine.suspend();
    valuesCards.suspend();
    if (!event.persisted) about.dispose();
    if (!event.persisted) program.dispose();
    if (!event.persisted) values.dispose();
    if (!event.persisted) valuesLine.dispose();
    if (!event.persisted) valuesCards.dispose();
    if (!event.persisted) { header.dispose(); hero.dispose(); cursor.dispose(); scrollIndicator.dispose(); scrollPacing.dispose(); }
});
window.addEventListener('pageshow', () => { gallery.resume(); testimonials.resume(); storiesHeading.resume(); storiesMedia.resume(); footerHover.resume(); scrollIndicator.resume(); scrollPacing.resume(); hero.resume(); about.resume(); program.resume(); values.resume(); valuesLine.resume(); valuesCards.resume(); });
