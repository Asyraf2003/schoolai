import { createHeroState } from './hero-state.js';
import { createHeroMedia } from './hero-media.js';

export function mountHero(root, { audioChanged }) {
    if (!root) return { toggleAudio() {}, boundary: () => null, suspend() {}, resume() {}, dispose() {} };
    const slides = [...root.querySelectorAll('[data-slide]')];
    const controls = root.querySelector('[data-hero-controls]');
    const playback = root.querySelector('[data-playback]');
    const live = root.querySelector('[data-hero-live]');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const cleanups = [];
    let timer;
    let observer;
    let pointer;
    let current = -1;
    const interval = Math.min(15000, Math.max(4000, Number(root.dataset.interval) || 7200));
    function listen(target, event, handler, options) {
        if (!target) return;
        target.addEventListener(event, handler, options);
        cleanups.push(() => target.removeEventListener(event, handler, options));
    }
    const media = createHeroMedia(slides, {
        onEnded: index => { if (state.snapshot().index === index) state.send({ type: 'advance' }); },
        onAudioBlocked: () => state.send({ type: 'audio-blocked' }),
        onFailure: index => { if (state.snapshot().index === index) state.send({ type: 'refresh' }); },
    });
    const state = createHeroState({ count: slides.length, reduced: reduced.matches, render(value, event) {
        clearTimeout(timer);
        const changed = current !== value.index;
        if (changed) {
            root.dataset.changing = String(current >= 0);
            current = value.index;
            slides.forEach((slide, index) => {
                const active = value.index === index;
                slide.setAttribute('aria-hidden', String(!active));
                slide.inert = !active;
                slide.querySelectorAll('a, button').forEach(control => {
                    if (active) control.removeAttribute('tabindex');
                    else control.tabIndex = -1;
                });
            });
        }
        const failed = media.failed(value.index);
        root.dataset.mediaState = failed ? 'fallback' : (value.canPlay ? 'active' : 'paused');
        playback.textContent = value.paused || failed ? playback.dataset.playLabel : playback.dataset.pauseLabel;
        playback.setAttribute('aria-pressed', String(value.paused));
        media.sync(value);
        audioChanged({ enabled: value.audio && !failed, available: media.isVideo(value.index) });
        if (event.type === 'step') {
            const label = root.dataset.slideLabel.replace(':current', String(value.index + 1)).replace(':total', String(slides.length));
            live.textContent = `${label}: ${slides[value.index].dataset.title}`;
        }
        if (value.canAdvance && (!media.isVideo(value.index) || failed)) timer = setTimeout(() => state.send({ type: 'advance' }), interval);
    } });
    const step = delta => {
        const focusInSlide = slides.some(slide => slide.contains(document.activeElement));
        state.send({ type: 'step', delta });
        if (focusInSlide) root.focus({ preventScroll: true });
    };
    listen(root.querySelector('[data-previous]'), 'click', () => step(-1));
    listen(root.querySelector('[data-next]'), 'click', () => step(1));
    listen(playback, 'click', () => {
        const value = state.snapshot();
        if (media.failed(value.index)) {
            media.retry(value.index);
            state.send({ type: 'environment', values: { paused: false } });
        } else state.send({ type: 'pause' });
    });
    listen(root, 'keydown', event => {
        if (event.altKey || event.ctrlKey || event.metaKey || !['ArrowLeft', 'ArrowRight'].includes(event.key) || slides.length < 2) return;
        event.preventDefault();
        const direction = document.documentElement.dir === 'rtl' ? -1 : 1;
        step((event.key === 'ArrowRight' ? 1 : -1) * direction);
    });
    listen(root, 'pointerdown', event => {
        if (event.pointerType === 'mouse' || event.target.closest('a, button')) return;
        pointer = { id: event.pointerId, x: event.clientX, y: event.clientY };
    }, { passive: true });
    listen(root, 'pointerup', event => {
        if (!pointer || pointer.id !== event.pointerId) return;
        const x = event.clientX - pointer.x;
        const y = event.clientY - pointer.y;
        pointer = null;
        if (slides.length > 1 && Math.abs(x) > 48 && Math.abs(x) > Math.abs(y)) step((x < 0 ? 1 : -1) * (document.documentElement.dir === 'rtl' ? -1 : 1));
    }, { passive: true });
    listen(root, 'pointercancel', () => { pointer = null; });
    listen(root, 'focusin', event => { if (!controls.contains(event.target)) state.send({ type: 'environment', values: { focused: true } }); });
    listen(root, 'focusout', event => {
        if (!root.contains(event.relatedTarget) || controls.contains(event.relatedTarget)) state.send({ type: 'environment', values: { focused: false } });
    });
    listen(document, 'visibilitychange', () => state.send({ type: 'environment', values: { visible: !document.hidden } }));
    const motionChange = () => state.send({ type: 'motion', reduced: reduced.matches });
    if (reduced.addEventListener) listen(reduced, 'change', motionChange);
    else { reduced.addListener(motionChange); cleanups.push(() => reduced.removeListener(motionChange)); }
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(entries => state.send({ type: 'environment', values: { inViewport: entries.some(entry => entry.isIntersecting) } }));
        observer.observe(root);
    }
    root.dataset.enhanced = 'true';
    controls.hidden = !(slides.length > 1 || slides.some(slide => slide.querySelector('video')));
    state.send({ type: 'environment', values: { visible: !document.hidden } });
    return {
        toggleAudio() { media.retry(state.snapshot().index); state.send({ type: 'audio' }); },
        boundary: () => root.getBoundingClientRect().bottom,
        suspend() { state.send({ type: 'environment', values: { suspended: true } }); },
        resume() { state.send({ type: 'environment', values: { suspended: false, visible: !document.hidden } }); },
        dispose() {
            clearTimeout(timer); observer?.disconnect(); media.dispose(); cleanups.forEach(cleanup => cleanup());
            delete root.dataset.enhanced;
            controls.hidden = true;
            slides.forEach(slide => { slide.removeAttribute('aria-hidden'); slide.inert = false; slide.querySelectorAll('a').forEach(link => link.removeAttribute('tabindex')); });
        },
    };
}
