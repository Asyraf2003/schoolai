const DESKTOP_QUERY = '(min-width: 1280px)';

const clamp = (value, min = 0, max = 1) => (
    Math.min(max, Math.max(min, value))
);

function smoothProgress(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

export function mountArticleStory(root) {
    const journey = root.querySelector('[data-article-journey]');
    const track = root.querySelector('[data-article-track]');
    const panels = [...root.querySelectorAll('[data-article-panel]')];
    const rollWindow = root.querySelector('[data-article-roll-window]');
    const rollStack = root.querySelector('[data-article-roll-stack]');
    const closing = root.querySelector('[data-article-closing]');
    const cta = root.querySelector('[data-article-final-cta]');
    const desktop = window.matchMedia(DESKTOP_QUERY);
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!journey || !track || panels.length === 0 || !rollWindow || !rollStack || !closing) {
        return () => {};
    }

    let frame = 0;
    let destroyed = false;
    let active = !('IntersectionObserver' in window);
    let observer = null;
    let trackDistance = 0;
    let rollDistance = 0;

    function enabled() {
        return desktop.matches && !reduced.matches;
    }

    function measure() {
        trackDistance = Math.max(0, closing.offsetLeft);
        rollDistance = Math.max(
            0,
            rollStack.scrollHeight - rollWindow.clientHeight + window.innerHeight * 0.12,
        );
    }

    function paintPanelMotion(trackX) {
        const viewportWidth = Math.max(1, window.innerWidth);
        const viewportCenter = viewportWidth * 0.5;

        panels.forEach((panel, index) => {
            const panelLeft = panel.offsetLeft + trackX;
            const mediaLeft = panelLeft + viewportWidth * 0.30;
            const mediaCenter = panelLeft + viewportWidth * 0.50;
            const relative = clamp(
                (mediaCenter - viewportCenter) / viewportWidth,
                -1,
                1,
            );
            const mediaX = clamp(relative * 8, -8, 8);

            let copyProgress;
            if (index === 0) {
                copyProgress = smoothProgress(clamp(
                    -panelLeft / (viewportWidth * 0.40),
                ));
            } else {
                copyProgress = smoothProgress(clamp(
                    ((viewportWidth * 0.95) - mediaLeft) / (viewportWidth * 0.40),
                ));
            }

            const movesUp = index % 2 === 0;
            const headingY = movesUp
                ? -20.833333 * copyProgress
                : 45 * copyProgress;
            const descriptionY = movesUp
                ? -33.333333 * copyProgress
                : 32.5 * copyProgress;

            panel.style.setProperty('--article-media-x', `${mediaX.toFixed(3)}%`);
            panel.style.setProperty('--article-heading-y', `${headingY.toFixed(3)}svh`);
            panel.style.setProperty(
                '--article-description-y',
                `${descriptionY.toFixed(3)}svh`,
            );
        });
    }

    function paintClosingMotion(trackX) {
        const viewportWidth = Math.max(1, window.innerWidth);
        const closingLeft = closing.offsetLeft + trackX;
        const entry = smoothProgress(clamp(
            (viewportWidth - closingLeft) / (viewportWidth * 0.80),
        ));

        root.style.setProperty(
            '--article-roll-y',
            `${(-rollDistance * entry).toFixed(2)}px`,
        );

        const ctaReady = closingLeft <= viewportWidth * 0.05;
        root.classList.toggle('is-article-cta-ready', ctaReady);
        if (cta) cta.tabIndex = ctaReady ? 0 : -1;
    }

    function paint(progress) {
        const horizontal = smoothProgress(progress);
        const trackX = -trackDistance * horizontal;

        root.style.setProperty('--article-track-x', `${trackX.toFixed(2)}px`);
        paintPanelMotion(trackX);
        paintClosingMotion(trackX);
    }

    function render() {
        frame = 0;
        if (destroyed || !active || document.hidden || !enabled()) return;
        const rect = journey.getBoundingClientRect();
        const travel = Math.max(1, journey.offsetHeight - window.innerHeight);
        paint(clamp(-rect.top / travel));
    }

    function requestRender() {
        if (!frame && active && enabled() && !destroyed) {
            frame = window.requestAnimationFrame(render);
        }
    }

    function resetPanelMotion() {
        panels.forEach((panel) => {
            panel.style.removeProperty('--article-media-x');
            panel.style.removeProperty('--article-heading-y');
            panel.style.removeProperty('--article-description-y');
        });
    }

    function reset() {
        root.classList.remove('is-article-cta-ready');
        root.style.removeProperty('--article-track-x');
        root.style.removeProperty('--article-roll-y');
        resetPanelMotion();
        if (cta) cta.removeAttribute('tabindex');
    }

    function syncMode() {
        root.classList.toggle('is-article-story-ready', enabled());
        if (!enabled()) {
            if (frame) window.cancelAnimationFrame(frame);
            frame = 0;
            reset();
            return;
        }
        measure();
        if (cta && !cta.contains(document.activeElement)) cta.tabIndex = -1;
        requestRender();
    }

    function onResize() {
        measure();
        requestRender();
    }

    function onVisibility() {
        if (document.hidden && frame) {
            window.cancelAnimationFrame(frame);
            frame = 0;
            return;
        }
        requestRender();
    }

    function onIntersection(entries) {
        active = entries.some((entry) => entry.isIntersecting);
        if (active) {
            measure();
            requestRender();
        }
    }

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '100% 0px 100% 0px',
            threshold: 0,
        });
        observer.observe(root);
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', onResize);
    document.addEventListener('visibilitychange', onVisibility);
    cta?.addEventListener('focusin', requestRender);
    cta?.addEventListener('focusout', requestRender);
    desktop.addEventListener('change', syncMode);
    reduced.addEventListener('change', syncMode);
    syncMode();

    return () => {
        destroyed = true;
        if (frame) window.cancelAnimationFrame(frame);
        observer?.disconnect();
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        cta?.removeEventListener('focusin', requestRender);
        cta?.removeEventListener('focusout', requestRender);
        desktop.removeEventListener('change', syncMode);
        reduced.removeEventListener('change', syncMode);
        reset();
    };
}

const articleStory = document.querySelector('[data-article-story]');
if (articleStory) mountArticleStory(articleStory);
