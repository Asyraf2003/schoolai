import { mountHeaderAccordion } from './header-accordion.js';
import { mountHeaderHighlight } from './header-highlight.js';
import { mountHeaderSound } from './header-sound.js';
import { mountHeaderLanguage } from './header-language.js';
import { createHeaderState } from './header-state.js';
import { mountMediaFallback } from './media-fallback.js';
import { mountHeaderMotion } from './header-motion.js';

export function mountHeader(root, { requestAudio, heroBoundary, modalChanged }) {
    if (!root) return { setAudio() {}, dispose() {} };
    const toggle = root.querySelector('[data-menu-toggle]');
    const audio = root.querySelector('[data-audio]');
    const nav = root.querySelector('nav');
    const groups = [...root.querySelectorAll('[data-panel]')];
    const media = window.matchMedia('(min-width: 1181px), (min-width: 1024px) and (orientation: landscape)');
    const cleanups = [mountHeaderMotion(root), mountMediaFallback(root)];
    const sound = mountHeaderSound(audio);
    let audioStatus = { enabled: false, available: false };
    let renderedPanel = null;
    const accordion = mountHeaderAccordion(groups, id => {
        renderedPanel = id;
        presentation(state.snapshot());
    });
    const highlight = mountHeaderHighlight(root);
    const language = mountHeaderLanguage(root, () => {
        syncAccess(state.snapshot());
        highlight.update(state.snapshot().panel ?? renderedPanel);
    });
    cleanups.push(() => sound.dispose(), () => language.dispose(), () => accordion.dispose(), () => highlight.dispose());
    function syncAccess(value) {
        document.documentElement.toggleAttribute('data-landing-menu-open', value.mobileOpen || root.dataset.languageOpen === 'true');
        modalChanged?.(value.mobileOpen);
    }
    function presentation(value) {
        const navigationOpen = value.mobileOpen || value.panel !== null || renderedPanel !== null;
        root.dataset.open = String(navigationOpen);
        highlight.update(value.panel ?? renderedPanel);
        sound.update(audioStatus, { ...value, navigationOpen });
    }
    let closing;
    let frame = 0;
    function listen(target, type, handler, options) {
        target.addEventListener(type, handler, options);
        cleanups.push(() => target.removeEventListener(type, handler, options));
    }
    const state = createHeaderState((value) => {
        const opening = value.mobileOpen && root.dataset.mobileOpen !== 'true';
        root.dataset.mode = value.desktop ? 'desktop' : 'compact';
        root.dataset.mobileOpen = String(value.mobileOpen);
        root.dataset.scrolled = String(value.scrolled);
        root.dataset.concealed = String(value.concealed);
        toggle.setAttribute('aria-expanded', String(value.mobileOpen));
        toggle.setAttribute('aria-label', value.mobileOpen ? toggle.dataset.closeLabel : toggle.dataset.openLabel);
        syncAccess(value);
        if (opening) root.dispatchEvent(new Event('menu:open'));
        accordion.sync(value.panel, value.desktop, value.desktop || value.mobileOpen);
        presentation(value);
    });
    function dismiss(restore = false) {
        const current = state.snapshot();
        const target = current.panel !== null ? groups.find(g => g.dataset.panel === current.panel)?.querySelector('summary') : toggle;
        if (closing) return;
        const finish = () => { closing = null; state.send({ type: 'dismiss' }); };
        if (current.mobileOpen && nav.animate && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            closing = nav.animate([{ transform: 'translateY(0)', opacity: 1 }, { transform: 'translateY(50vh)', opacity: 0 }], { duration: 280, easing: 'ease-in' });
            closing.finished.then(finish, () => { closing = null; });
        } else finish();
        if (restore) (current.mobileOpen ? toggle : target)?.focus();
    }
    function updateViewport() {
        closing?.cancel();
        const focusWasInside = nav.contains(document.activeElement);
        language.close();
        state.send({ type: 'viewport', desktop: media.matches });
        if (!media.matches && focusWasInside) toggle.focus();
        updateScroll();
    }
    function updateScroll() {
        if (frame) return;
        frame = requestAnimationFrame(() => {
            frame = 0;
            const bottom = heroBoundary();
            state.send({ type: 'scroll', y: window.scrollY,
                outsideHero: bottom !== null && bottom <= 0,
                focused: renderedPanel !== null || root.contains(document.activeElement) });
        });
    }
    listen(toggle, 'click', () => {
        if (state.snapshot().mobileOpen) { dismiss(true); return; }
        state.send({ type: 'mobile' });
        if (state.snapshot().mobileOpen) nav.querySelector('a, summary')?.focus();
    });
    groups.forEach(group => listen(group.querySelector('summary'), 'click', event => {
        event.preventDefault();
        closing?.cancel();
        state.send({ type: 'panel', id: group.dataset.panel, open: state.snapshot().panel !== group.dataset.panel });
    }));
    listen(root, 'focusin', () => state.send({ type: 'focus' }));
    listen(root, 'click', (event) => {
        if (event.target.closest('a[href]')) dismiss(true);
    });
    listen(document, 'click', (event) => { if (!root.contains(event.target)) dismiss(); });
    listen(document, 'keydown', (event) => {
        const value = state.snapshot();
        if (language.isOpen()) return;
        if (event.key === 'Escape' && (value.mobileOpen || value.panel !== null)) {
            event.preventDefault();
            dismiss(true);
        }
        if (event.key !== 'Tab' || !value.mobileOpen) return;
        const controls = [...root.querySelectorAll('a[href], button:not([disabled]), summary')]
            .filter(element => element.getClientRects().length);
        const first = controls[0];
        const last = controls.at(-1);
        if (event.shiftKey && (document.activeElement === first || !root.contains(document.activeElement))) {
            event.preventDefault(); last?.focus();
        } else if (!event.shiftKey && (document.activeElement === last || !root.contains(document.activeElement))) {
            event.preventDefault(); first?.focus();
        }
    });
    listen(audio, 'click', requestAudio);
    listen(window, 'scroll', updateScroll, { passive: true });
    listen(window, 'resize', updateScroll, { passive: true });
    if (media.addEventListener) listen(media, 'change', updateViewport);
    else { media.addListener(updateViewport); cleanups.push(() => media.removeListener(updateViewport)); }
    root.dataset.enhanced = 'true';
    toggle.hidden = false;
    updateViewport();
    return {
        setAudio({ enabled, available }) {
            audioStatus = { enabled, available };
            audio.hidden = !available;
            audio.setAttribute('aria-pressed', String(enabled));
            audio.querySelector('[data-audio-label]').textContent = enabled ? audio.dataset.labelOn : audio.dataset.labelOff;
            audio.setAttribute('aria-label', enabled ? audio.dataset.actionOn : audio.dataset.actionOff);
            presentation(state.snapshot());
        },
        dispose() {
            closing?.cancel();
            state.send({ type: 'dismiss' });
            cancelAnimationFrame(frame);
            cleanups.forEach(cleanup => cleanup());
            delete root.dataset.enhanced;
            delete root.dataset.mode;
            toggle.hidden = true;
            audio.hidden = true;
        },
    };
}
