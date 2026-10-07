import { createAboutMedia } from './about-media.js';
import { mountAboutDialog } from './about-dialog.js';
import { observeAboutStories } from './about-story.js';

export function mountAbout(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const controller = new AbortController();
    const { signal } = controller;
    const stories = [...root.querySelectorAll('[data-about-story]')];
    const stage = root.querySelector('[data-about-stage]');
    const stageButton = root.querySelector('[data-about-stage-open]');
    const layout = matchMedia('(min-width: 1024px)');
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const media = createAboutMedia(root, stories, signal);
    const visibility = new Map();
    const nearby = new Set();
    let active = stories[0];
    let near = false;
    let modal = false;
    let suspended = false;
    let stopStories;
    let viewObserver;
    let proximity;
    const enhanced = 'IntersectionObserver' in window;
    const isWide = () => enhanced && layout.matches;
    const render = () => {
        const wide = isWide();
        const target = wide ? stage : active.querySelector('[data-about-inline]');
        const visible = visibility.get(target) ?? false;
        stageButton.hidden = !wide || !dialog.supported || !active.dataset.full;
        if (!stageButton.hidden) stageButton.setAttribute('aria-label', active.dataset.openLabel);
        root.dataset.activeStory = active.dataset.aboutStory;
        const prepare = enhanced && !motion.matches && (wide ? near : nearby.has(active));
        media.update(active, {
            wide, prepare,
            play: prepare && visible && !modal && !suspended && !document.hidden,
        });
    };
    const dialog = mountAboutDialog(root, open => { modal = open; render(); }, signal);
    root.addEventListener('click', event => {
        const trigger = event.target.closest('[data-about-open], [data-about-stage-open]');
        if (trigger) dialog.open(trigger === stageButton ? active : trigger.closest('[data-about-story]'), trigger);
    }, { signal });
    const configure = () => {
        stopStories?.();
        viewObserver?.disconnect();
        proximity?.disconnect();
        visibility.clear();
        nearby.clear();
        media.pause();
        root.dataset.wide = String(isWide());
        stage.hidden = !isWide();
        if (!enhanced) { render(); return; }
        stopStories = observeAboutStories(stories, isWide(), story => { active = story; render(); });
        viewObserver = new IntersectionObserver(changes => {
            changes.forEach(entry => visibility.set(entry.target, entry.isIntersecting));
            render();
        });
        const targets = isWide() ? [stage] : stories.map(story => story.querySelector('[data-about-inline]'));
        targets.forEach(target => viewObserver.observe(target));
        proximity = new IntersectionObserver(changes => {
            changes.forEach(entry => {
                if (isWide()) near = entry.isIntersecting && entry.intersectionRatio > 0;
                else {
                    const story = entry.target.closest('[data-about-story]');
                    if (entry.isIntersecting && entry.intersectionRatio > 0) {
                        nearby.add(story);
                        if (!motion.matches) media.prepareInline(story);
                    } else nearby.delete(story);
                }
                if (entry.isIntersecting) root.style.setProperty('--about-background', `url("${root.dataset.background}")`);
            });
            render();
        }, { rootMargin: '0px 0px -15% 0px' });
        (isWide() ? [root] : targets).forEach(target => proximity.observe(target));
        render();
    };
    layout.addEventListener('change', configure, { signal });
    window.addEventListener('resize', configure, { signal });
    motion.addEventListener('change', render, { signal });
    document.addEventListener('visibilitychange', render, { signal });
    configure();
    return {
        suspend() { suspended = true; dialog.suspend(); media.pause(); },
        resume() { suspended = false; render(); },
        dispose() {
            suspended = true;
            dialog.suspend();
            controller.abort();
            stopStories?.(); viewObserver?.disconnect(); proximity?.disconnect();
            media.dispose();
        },
    };
}
