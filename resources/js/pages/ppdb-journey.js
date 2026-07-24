import { createPanelState, rebuildGeometry, updateConnectorGeometry } from './ppdb-journey/panel-state.js';
import { drawState, updateTargets } from './ppdb-journey/progress.js';

const desktopJourney = window.matchMedia('(min-width: 901px) and (prefers-reduced-motion: no-preference)');

if (desktopJourney.matches) {
    const originalRoot = document.querySelector('[data-ppdb-liftoff]');

    if (originalRoot) {
        const root = originalRoot.cloneNode(true);
        originalRoot.replaceWith(root);
        root.classList.add('ppdb-journey-v3');

        root.querySelectorAll('[data-ppdb-storyline]').forEach((storyline) => {
            storyline.replaceChildren();
            storyline.setAttribute('hidden', '');
        });

        root.querySelectorAll('[data-ppdb-story-node]').forEach((node) => {
            node.classList.remove('is-story-lit', 'is-story-holding');
        });

        const tabs = Array.from(root.querySelectorAll('[data-ppdb-liftoff-tab]'));
        const panels = Array.from(root.querySelectorAll('[data-ppdb-liftoff-panel]'));
        const panelStates = new WeakMap();
        let frameRequest = 0;
        let needsGeometry = true;
        let needsTargets = true;

        const activePanel = () => root.querySelector(`[data-ppdb-liftoff-panel="${root.dataset.activeAudience}"]`);

        const render = () => {
            frameRequest = 0;
            const panel = activePanel();
            const state = panel ? panelStates.get(panel) : null;
            if (!state) return;

            if (needsGeometry) {
                rebuildGeometry(state);
                needsGeometry = false;
                needsTargets = true;
            }

            if (needsTargets) {
                updateTargets(state);
                needsTargets = false;
            }

            updateConnectorGeometry(state);
            const unsettled = drawState(state);
            if (unsettled) frameRequest = window.requestAnimationFrame(render);
        };

        const schedule = ({ geometry = false } = {}) => {
            needsGeometry = needsGeometry || geometry;
            needsTargets = true;
            if (!frameRequest) frameRequest = window.requestAnimationFrame(render);
        };

        const activate = (audience) => {
            const target = root.querySelector(`[data-ppdb-liftoff-panel="${audience}"]`);
            if (!target) return;

            root.dataset.activeAudience = audience;
            tabs.forEach((tab) => {
                const selected = tab.dataset.ppdbLiftoffTab === audience;
                tab.classList.toggle('is-active', selected);
                tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
            panels.forEach((panel) => {
                panel.hidden = panel !== target;
            });

            window.requestAnimationFrame(() => schedule({ geometry: true }));
        };

        panels.forEach((panel) => createPanelState(panel, panelStates));
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                if (!tab.disabled) activate(tab.dataset.ppdbLiftoffTab);
            });
        });

        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) entry.target.classList.add('is-visible');
                });
            }, { rootMargin: '20% 0px 20% 0px', threshold: 0.01 });
            root.querySelectorAll('.reveal').forEach((element) => revealObserver.observe(element));
        } else {
            root.querySelectorAll('.reveal').forEach((element) => element.classList.add('is-visible'));
        }

        window.addEventListener('scroll', () => schedule(), { passive: true });
        window.addEventListener('resize', () => schedule({ geometry: true }));

        if ('ResizeObserver' in window) {
            const resizeObserver = new ResizeObserver(() => schedule({ geometry: true }));
            panels.forEach((panel) => resizeObserver.observe(panel));
        }

        schedule({ geometry: true });
    }
}
