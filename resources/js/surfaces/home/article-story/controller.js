const DESKTOP_QUERY = '(min-width: 1280px)';

const clamp = (value, min = 0, max = 1) => (
    Math.min(max, Math.max(min, value))
);

export function mountArticleStory(root) {
    const stack = root.querySelector('[data-article-stack]');
    const items = [...root.querySelectorAll('[data-article-sticky]')];
    const desktop = window.matchMedia(DESKTOP_QUERY);
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (!stack || items.length === 0) return () => {};

    let frame = 0;
    let destroyed = false;
    let active = !('IntersectionObserver' in window);
    let observer = null;

    function enabled() {
        return desktop.matches && !reduced.matches;
    }

    function reset() {
        items.forEach((item) => {
            item.style.removeProperty('--article-sticky-scale');
            delete item.dataset.articleStickyProgress;
        });
    }

    function paint() {
        frame = 0;
        if (destroyed || !active || document.hidden || !enabled()) return;

        const stackRect = stack.getBoundingClientRect();
        const localScroll = -stackRect.top;
        const viewportHeight = Math.max(1, window.innerHeight);

        items.forEach((item) => {
            const start = item.offsetTop;
            const progress = clamp((localScroll - start) / viewportHeight);
            const scale = 1 - progress;

            item.style.setProperty(
                '--article-sticky-scale',
                scale.toFixed(4),
            );
            item.dataset.articleStickyProgress = progress.toFixed(4);
        });
    }

    function requestPaint() {
        if (!frame && active && enabled() && !destroyed) {
            frame = window.requestAnimationFrame(paint);
        }
    }

    function syncMode() {
        root.classList.toggle('is-article-story-ready', enabled());
        if (!enabled()) {
            if (frame) window.cancelAnimationFrame(frame);
            frame = 0;
            reset();
            return;
        }
        requestPaint();
    }

    function onVisibility() {
        if (document.hidden && frame) {
            window.cancelAnimationFrame(frame);
            frame = 0;
            return;
        }
        requestPaint();
    }

    function onIntersection(entries) {
        active = entries.some((entry) => entry.isIntersecting);
        if (active) requestPaint();
    }

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '100% 0px 100% 0px',
            threshold: 0,
        });
        observer.observe(root);
    }

    window.addEventListener('scroll', requestPaint, { passive: true });
    window.addEventListener('resize', requestPaint, { passive: true });
    window.addEventListener('pageshow', requestPaint);
    document.addEventListener('visibilitychange', onVisibility);
    desktop.addEventListener('change', syncMode);
    reduced.addEventListener('change', syncMode);
    syncMode();

    return () => {
        destroyed = true;
        if (frame) window.cancelAnimationFrame(frame);
        observer?.disconnect();
        window.removeEventListener('scroll', requestPaint);
        window.removeEventListener('resize', requestPaint);
        window.removeEventListener('pageshow', requestPaint);
        document.removeEventListener('visibilitychange', onVisibility);
        desktop.removeEventListener('change', syncMode);
        reduced.removeEventListener('change', syncMode);
        reset();
    };
}

const articleStory = document.querySelector('[data-article-story]');
if (articleStory) mountArticleStory(articleStory);
