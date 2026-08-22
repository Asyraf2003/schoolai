const DESKTOP_QUERY = '(min-width: 1280px)';
const HORIZONTAL_END = 0.78;
const HANDOFF_HOLD_VIEWPORTS = 1;

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
    const mainItems = [...root.querySelectorAll('[data-article-main-item]')];
    const rollWindow = root.querySelector('[data-article-roll-window]');
    const rollStack = root.querySelector('[data-article-roll-stack]');
    const closing = root.querySelector('[data-article-closing]');
    const cta = root.querySelector('[data-article-final-cta]');
    const desktop = window.matchMedia(DESKTOP_QUERY);
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!journey || !track || mainItems.length === 0 || !rollWindow || !rollStack || !closing) {
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

    function paintMainMotion(trackX) {
        const viewportWidth = Math.max(1, window.innerWidth);
        const viewportHeight = Math.max(1, window.innerHeight);
        const viewportCenter = viewportWidth * 0.5;
        const mediaWidth = viewportHeight + viewportWidth * 0.10;
        const copyTopMargin = viewportHeight / 24;

        mainItems.forEach((item, index) => {
            const itemLeft = item.offsetLeft + trackX;
            const mediaCenter = itemLeft + mediaWidth * 0.5;
            const mediaRight = itemLeft + mediaWidth;
            const relative = clamp(
                (mediaCenter - viewportCenter) / viewportWidth,
                -1,
                1,
            );
            const mediaX = clamp(relative * 8, -8, 8);
            const copy = item.querySelector('[data-article-main-copy]');

            item.style.setProperty(
                '--article-main-media-x',
                `${mediaX.toFixed(3)}%`,
            );

            if (!copy) return;

            const copyProgress = smoothProgress(clamp(
                ((viewportWidth * 0.95) - mediaRight) / (viewportWidth * 0.40),
            ));
            const centeredTop = viewportHeight * 0.5 - copy.offsetHeight * 0.5;
            const targetTop = index % 2 === 0
                ? copyTopMargin
                : viewportHeight - copyTopMargin - copy.offsetHeight;
            const copyY = (targetTop - centeredTop) * copyProgress;

            item.style.setProperty(
                '--article-main-copy-y',
                `${copyY.toFixed(2)}px`,
            );
        });
    }

    function paintClosingMotion(horizontalProgress, rollProgress) {
        root.style.setProperty(
            '--article-roll-y',
            `${(-rollDistance * rollProgress).toFixed(2)}px`,
        );

        const ctaReady = horizontalProgress > 0.995 && rollProgress > 0.12;
        root.classList.toggle('is-article-cta-ready', ctaReady);
        if (cta) cta.tabIndex = ctaReady ? 0 : -1;
    }

    function paint(progress) {
        const horizontalProgress = smoothProgress(clamp(progress / HORIZONTAL_END));
        const rollProgress = smoothProgress(clamp(
            (progress - HORIZONTAL_END) / (1 - HORIZONTAL_END),
        ));
        const trackX = -trackDistance * horizontalProgress;

        root.style.setProperty('--article-track-x', `${trackX.toFixed(2)}px`);
        paintMainMotion(trackX);
        paintClosingMotion(horizontalProgress, rollProgress);
    }

    function render() {
        frame = 0;
        if (destroyed || !active || document.hidden || !enabled()) return;

        const rect = journey.getBoundingClientRect();
        const handoffHold = window.innerHeight * HANDOFF_HOLD_VIEWPORTS;
        const travelled = Math.max(0, -rect.top - handoffHold);
        const travel = Math.max(
            1,
            journey.offsetHeight - window.innerHeight - handoffHold,
        );

        paint(clamp(travelled / travel));
    }

    function requestRender() {
        if (!frame && active && enabled() && !destroyed) {
            frame = window.requestAnimationFrame(render);
        }
    }

    function resetMainMotion() {
        mainItems.forEach((item) => {
            item.style.removeProperty('--article-main-media-x');
            item.style.removeProperty('--article-main-copy-y');
        });
    }

    function reset() {
        root.classList.remove('is-article-cta-ready');
        root.style.removeProperty('--article-track-x');
        root.style.removeProperty('--article-roll-y');
        resetMainMotion();
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
