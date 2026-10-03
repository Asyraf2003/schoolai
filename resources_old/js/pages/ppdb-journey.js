import { createPanelState, destroyPanelState, rebuildGeometry, resetPanelState } from './ppdb-journey/panel-state.js';
import { advanceTarget, canConsumeDirection, isJourneyReleaseReady, renderFrame, renderProgress } from './ppdb-journey/progress.js';
import { createJourneyReleaseState } from './ppdb-journey/release-state.js';
const mediaQuery = window.matchMedia('(min-width: 901px) and (prefers-reduced-motion: no-preference)');
const root = document.querySelector('[data-ppdb-liftoff]');
if (root && root.dataset.ppdbJourneyInitialized !== 'true') {
    root.dataset.ppdbJourneyInitialized = 'true';
    const stage = root.querySelector('[data-ppdb-journey-stage]');
    const status = root.querySelector('[data-ppdb-journey-status]');
    const cta = root.querySelector('.ppdb-liftoff__cta');
    const tabs = Array.from(root.querySelectorAll('[data-ppdb-liftoff-tab]'));
    const panels = Array.from(root.querySelectorAll('[data-ppdb-liftoff-panel]'));
    const states = new Map();
    const releaseState = createJourneyReleaseState(root);
    let enhanced = false;
    let pinned = false;
    let inputAttached = false;
    let renderRequest = 0;
    let pinRequest = 0;
    const activePanel = () => root.querySelector(
        `[data-ppdb-liftoff-panel="${root.dataset.activeAudience}"]`,
    );
    const ensureState = (panel) => {
        if (!panel) return null;
        if (!states.has(panel)) states.set(panel, createPanelState(panel));
        return states.get(panel);
    };
    const activeState = () => ensureState(activePanel());
    const render = (timestamp) => {
        renderRequest = 0;
        if (!enhanced) return;
        const state = activeState();
        if (!state) return;
        if (state.geometryDirty) rebuildGeometry(state);
        const unsettled = renderFrame(state, timestamp, status, cta);
        releaseState.setReleased(isJourneyReleaseReady(state));
        if (unsettled) renderRequest = window.requestAnimationFrame(render);
    };
    const scheduleRender = () => {
        if (!renderRequest) renderRequest = window.requestAnimationFrame(render);
    };
    const markGeometry = () => {
        states.forEach((state) => {
            if (state) state.geometryDirty = true;
        });
        scheduleRender();
    };
    const normalizeWheel = (event) => {
        let delta = event.deltaY;
        if (event.deltaMode === 1) delta *= 16;
        if (event.deltaMode === 2) delta *= window.innerHeight;
        return Math.min(Math.max(delta, -96), 96);
    };
    const keyboardDelta = (event) => ({
        ArrowDown: 240,
        ArrowUp: -240,
        PageDown: 720,
        PageUp: -720,
        ' ': event.shiftKey ? -720 : 720,
    })[event.key] || 0;
    const typingTarget = (target) => target instanceof Element && Boolean(
        target.closest('input, textarea, select, [contenteditable="true"], [role="tablist"]'),
    );
    const consume = (delta, event) => {
        const state = activeState();
        if (!delta || !pinned || !canConsumeDirection(state, Math.sign(delta))) return;
        event.preventDefault();
        advanceTarget(state, delta);
        scheduleRender();
    };
    const onWheel = (event) => {
        if (event.ctrlKey || event.metaKey) return;
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) return;
        consume(normalizeWheel(event), event);
    };
    const onKeyDown = (event) => {
        if (event.altKey || event.ctrlKey || event.metaKey || typingTarget(event.target)) return;
        consume(keyboardDelta(event), event);
    };
    const attachInput = () => {
        if (inputAttached) return;
        inputAttached = true;
        window.addEventListener('wheel', onWheel, { passive: false });
        window.addEventListener('keydown', onKeyDown);
    };
    const detachInput = () => {
        if (!inputAttached) return;
        inputAttached = false;
        window.removeEventListener('wheel', onWheel);
        window.removeEventListener('keydown', onKeyDown);
    };
    const measurePinned = () => {
        pinRequest = 0;
        if (!enhanced || !stage) return;
        const navbar = document.getElementById('navbar');
        const offset = Math.max(navbar?.getBoundingClientRect().height || 0, 0);
        root.style.setProperty('--ppdb-journey-nav-offset', `${offset}px`);
        const rootRect = root.getBoundingClientRect();
        const stageRect = stage.getBoundingClientRect();
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const next = !releaseState.isReleased()
            && Math.abs(stageRect.top - offset) <= 2
            && rootRect.top <= offset + 2
            && rootRect.bottom >= viewportHeight - 2;
        if (next === pinned) return;
        pinned = next;
        root.classList.toggle('is-journey-pinned', pinned);
        if (pinned) attachInput();
        else detachInput();
    };
    const schedulePin = () => {
        if (!pinRequest) pinRequest = window.requestAnimationFrame(measurePinned);
    };
    const activate = (audience) => {
        const target = root.querySelector(`[data-ppdb-liftoff-panel="${audience}"]`);
        if (!target) return;
        releaseState.reset();
        root.dataset.activeAudience = audience;
        tabs.forEach((tab) => {
            const selected = tab.dataset.ppdbLiftoffTab === audience;
            tab.classList.toggle('is-active', selected);
            tab.setAttribute('aria-selected', selected ? 'true' : 'false');
        });
        panels.forEach((panel) => {
            panel.hidden = panel !== target;
        });
        if (!enhanced) return;
        const state = ensureState(target);
        resetPanelState(state);
        if (cta) cta.removeAttribute('style');
        window.requestAnimationFrame(() => {
            rebuildGeometry(state);
            renderProgress(state, status, cta);
            schedulePin();
            scheduleRender();
        });
    };
    tabs.forEach((tab) => tab.addEventListener('click', () => {
        if (!tab.disabled) activate(tab.dataset.ppdbLiftoffTab);
    }));
    const reveal = Array.from(root.querySelectorAll('.reveal'));
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) entry.target.classList.add('is-visible');
            });
        }, { rootMargin: '24% 0px 24% 0px', threshold: 0.01 });
        reveal.forEach((element) => observer.observe(element));
    } else {
        reveal.forEach((element) => element.classList.add('is-visible'));
    }
    const enable = () => {
        if (enhanced || !stage) return;
        enhanced = true;
        releaseState.reset();
        root.classList.add('ppdb-journey-native');
        reveal.forEach((element) => element.classList.add('is-visible'));
        resetPanelState(activeState());
        markGeometry();
        schedulePin();
    };
    const disable = () => {
        if (!enhanced) return;
        enhanced = false;
        pinned = false;
        releaseState.reset();
        detachInput();
        if (renderRequest) window.cancelAnimationFrame(renderRequest);
        states.forEach((state) => destroyPanelState(state));
        states.clear();
        root.classList.remove('ppdb-journey-native', 'is-journey-pinned');
        root.style.removeProperty('--ppdb-journey-nav-offset');
        root.querySelectorAll('[data-ppdb-journey-card]').forEach((card) => {
            card.inert = false;
            card.removeAttribute('style');
        });
        if (cta) cta.removeAttribute('style');
        if (status) status.textContent = '';
    };
    const syncMode = () => (mediaQuery.matches ? enable() : disable());
    window.addEventListener('scroll', schedulePin, { passive: true });
    window.addEventListener('resize', () => {
        markGeometry();
        schedulePin();
    });
    if ('ResizeObserver' in window && stage) {
        const resizeObserver = new ResizeObserver(markGeometry);
        resizeObserver.observe(stage);
        panels.forEach((panel) => resizeObserver.observe(panel));
    }
    if (mediaQuery.addEventListener) mediaQuery.addEventListener('change', syncMode);
    else mediaQuery.addListener?.(syncMode);
    syncMode();
}
