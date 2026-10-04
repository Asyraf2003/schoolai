import { createHeaderState } from './header-state.js';
import { mountHeaderMotion } from './header-motion.js';

export function mountHeader(root, { requestAudio, heroBoundary, modalChanged }) {
    if (!root) return { setAudio() {}, dispose() {} };
    const toggle = root.querySelector('[data-menu-toggle]');
    const audio = root.querySelector('[data-audio]');
    const nav = root.querySelector('nav');
    const groups = [...root.querySelectorAll('[data-panel]')];
    const media = window.matchMedia('(min-width: 1181px)');
    const cleanups = [mountHeaderMotion(root)];
    let frame = 0;
    function listen(target, type, handler, options) {
        target.addEventListener(type, handler, options);
        cleanups.push(() => target.removeEventListener(type, handler, options));
    }
    const state = createHeaderState((value) => {
        root.dataset.mode = value.desktop ? 'desktop' : 'compact';
        root.dataset.mobileOpen = String(value.mobileOpen);
        root.dataset.open = String(value.mobileOpen || value.panel !== null);
        root.dataset.scrolled = String(value.scrolled);
        root.dataset.concealed = String(value.concealed);
        toggle.setAttribute('aria-expanded', String(value.mobileOpen));
        toggle.setAttribute('aria-label', value.mobileOpen ? toggle.dataset.closeLabel : toggle.dataset.openLabel);
        document.documentElement.toggleAttribute('data-landing-menu-open', value.mobileOpen);
        modalChanged?.(value.mobileOpen);
        groups.forEach((group) => { group.open = group.dataset.panel === value.panel; });
    });
    function dismiss(restore = false) {
        const current = state.snapshot();
        const target = current.panel !== null ? groups.find(g => g.dataset.panel === current.panel)?.querySelector('summary') : toggle;
        state.send({ type: 'dismiss' });
        if (restore) (current.mobileOpen ? toggle : target)?.focus();
    }
    function updateViewport() {
        const focusWasInside = nav.contains(document.activeElement);
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
                outsideHero: bottom !== null && bottom <= root.offsetHeight,
                focused: root.contains(document.activeElement) });
        });
    }
    listen(toggle, 'click', () => {
        state.send({ type: 'mobile' });
        if (state.snapshot().mobileOpen) nav.querySelector('a, summary')?.focus();
    });
    groups.forEach(group => listen(group, 'toggle', () => {
        const active = state.snapshot().panel === group.dataset.panel;
        if (active !== group.open) state.send({ type: 'panel', id: group.dataset.panel, open: group.open });
    }));
    listen(root, 'focusin', () => state.send({ type: 'focus' }));
    listen(root, 'click', (event) => {
        if (event.target.closest('a[href]')) dismiss(true);
    });
    listen(document, 'click', (event) => { if (!root.contains(event.target)) dismiss(); });
    listen(document, 'keydown', (event) => {
        const value = state.snapshot();
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
            audio.hidden = !available;
            audio.setAttribute('aria-pressed', String(enabled));
            audio.querySelector('[data-audio-label]').textContent = enabled ? audio.dataset.labelOn : audio.dataset.labelOff;
            audio.setAttribute('aria-label', enabled ? audio.dataset.actionOn : audio.dataset.actionOff);
        },
        dispose() {
            dismiss();
            cancelAnimationFrame(frame);
            cleanups.forEach(cleanup => cleanup());
            delete root.dataset.enhanced;
            delete root.dataset.mode;
            toggle.hidden = true;
            audio.hidden = true;
        },
    };
}
