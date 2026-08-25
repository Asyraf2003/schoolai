let mounted = false;

const BLUE = [32, 56, 255];
const GALLERY = [239, 184, 75];

function clamp(value) {
    return Math.max(0, Math.min(1, value));
}

function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - (2 * progress));
}

function mixColor(from, to, progress) {
    const value = smoothstep(progress);
    const channels = from.map((channel, index) => Math.round(
        channel + ((to[index] - channel) * value),
    ));

    return `rgb(${channels.join(' ')})`;
}

function readValuesExitProgress(valuesWorld) {
    if (!valuesWorld) return 0;
    const raw = getComputedStyle(valuesWorld)
        .getPropertyValue('--values-gallery-exit-progress')
        .trim();
    const value = Number.parseFloat(raw);
    return Number.isFinite(value) ? clamp(value) : 0;
}

function titleFinalScale() {
    if (window.innerWidth < 640) return 0.58;
    if (window.innerWidth < 1024) return 0.50;
    return 0.42;
}

function mountGalleryStory(root) {
    if (mounted) return;
    mounted = true;

    const section = root.closest('.galeri-section') || root;
    const intro = root.querySelector('[data-gallery-story-intro]');
    const items = Array.from(root.querySelectorAll('[data-gallery-story-item]'));
    const valuesWorld = document.querySelector('[data-program-values-world]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let frame = 0;
    let destroyed = false;

    function paintHandoff() {
        const exitProgress = readValuesExitProgress(valuesWorld);
        const sectionTop = section.getBoundingClientRect().top;
        const desktop = window.innerWidth >= 1280;
        const active = desktop
            && !reducedMotion.matches
            && exitProgress > 0.0001
            && sectionTop > 1;

        section.classList.toggle('is-gallery-handoff', active);
        root.style.setProperty(
            '--gallery-handoff-color',
            mixColor(BLUE, GALLERY, exitProgress),
        );
        root.style.setProperty('--gallery-handoff-opacity', active ? '1' : '0');

        if (active) {
            const headingProgress = smoothstep((exitProgress - 0.68) / 0.32);
            root.style.setProperty('--gallery-title-y', '0px');
            root.style.setProperty(
                '--gallery-title-scale',
                (1.18 + (headingProgress * 0.08)).toFixed(4),
            );
            root.style.setProperty(
                '--gallery-title-opacity',
                headingProgress.toFixed(4),
            );
        }

        return active;
    }

    function paintIntro(handoffActive) {
        if (!intro || handoffActive || reducedMotion.matches) return;

        const viewportHeight = Math.max(window.innerHeight, 1);
        const rect = intro.getBoundingClientRect();
        const travel = Math.max(intro.offsetHeight - viewportHeight, 1);
        const progress = smoothstep(clamp(-rect.top / travel));
        const startY = viewportHeight * (window.innerWidth < 640 ? 0.34 : 0.36);
        const scale = 1.26 + ((titleFinalScale() - 1.26) * progress);

        root.style.setProperty(
            '--gallery-title-y',
            `${(startY * (1 - progress)).toFixed(2)}px`,
        );
        root.style.setProperty('--gallery-title-scale', scale.toFixed(4));
        root.style.setProperty('--gallery-title-opacity', '1');
    }

    function paintItems() {
        if (reducedMotion.matches) return;
        const viewportHeight = Math.max(window.innerHeight, 1);
        const trigger = viewportHeight * 0.91;
        const distance = viewportHeight * 0.72;

        items.forEach((item) => {
            const rect = item.getBoundingClientRect();
            const raw = clamp((trigger - rect.top) / distance);
            const mediaProgress = smoothstep(raw);
            const copyProgress = smoothstep(clamp((raw - 0.16) / 0.84));
            const mediaY = (1 - mediaProgress) * Math.min(190, viewportHeight * 0.22);
            const mediaScale = 0.94 + (mediaProgress * 0.06);
            const copyY = (1 - copyProgress) * Math.min(112, viewportHeight * 0.13);

            item.style.setProperty('--gallery-media-y', `${mediaY.toFixed(2)}px`);
            item.style.setProperty('--gallery-media-scale', mediaScale.toFixed(4));
            item.style.setProperty('--gallery-media-opacity', mediaProgress.toFixed(4));
            item.style.setProperty('--gallery-copy-y', `${copyY.toFixed(2)}px`);
            item.style.setProperty('--gallery-copy-opacity', copyProgress.toFixed(4));
        });
    }

    function render() {
        frame = 0;
        if (destroyed || document.hidden) return;
        const handoffActive = paintHandoff();
        paintIntro(handoffActive);
        paintItems();
        if (handoffActive) frame = window.requestAnimationFrame(render);
    }

    function requestRender() {
        if (!frame && !destroyed) frame = window.requestAnimationFrame(render);
    }

    function paintStatic() {
        section.classList.remove('is-gallery-handoff');
        root.style.setProperty('--gallery-title-opacity', '1');
        items.forEach((item) => {
            item.style.setProperty('--gallery-media-y', '0px');
            item.style.setProperty('--gallery-media-scale', '1');
            item.style.setProperty('--gallery-media-opacity', '1');
            item.style.setProperty('--gallery-copy-y', '0px');
            item.style.setProperty('--gallery-copy-opacity', '1');
        });
    }

    function onMotionChange() {
        if (reducedMotion.matches) paintStatic();
        else requestRender();
    }

    function destroy(event) {
        if (event?.persisted) return;
        destroyed = true;
        if (frame) window.cancelAnimationFrame(frame);
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', requestRender);
        window.removeEventListener('pageshow', requestRender);
        window.removeEventListener('pagehide', destroy);
        reducedMotion.removeEventListener?.('change', onMotionChange);
    }

    if (reducedMotion.matches) paintStatic();
    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender, { passive: true });
    window.addEventListener('pageshow', requestRender);
    window.addEventListener('pagehide', destroy);
    reducedMotion.addEventListener?.('change', onMotionChange);
    requestRender();
}

export function prepareHomepageDepthGallery() {
    const root = document.querySelector('[data-gallery-story]');
    if (!root) return Promise.resolve(null);
    mountGalleryStory(root);
    return Promise.resolve(root);
}
