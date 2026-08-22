const DESKTOP_QUERY = '(min-width: 1280px)';

const clamp = (value, min = 0, max = 1) => (
    Math.min(max, Math.max(min, value))
);

function phase(progress, start, end) {
    const value = clamp((progress - start) / Math.max(0.0001, end - start));
    return value * value * (3 - 2 * value);
}

function setNumber(root, name, value) {
    root.style.setProperty(name, value.toFixed(4));
}

export function mountArticleStory(root) {
    const journey = root.querySelector('[data-article-journey]');
    const track = root.querySelector('[data-article-track]');
    const rollWindow = root.querySelector('[data-article-roll-window]');
    const rollStack = root.querySelector('[data-article-roll-stack]');
    const cta = root.querySelector('[data-article-final-cta]');
    const desktop = window.matchMedia(DESKTOP_QUERY);
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (!journey || !track || !rollWindow || !rollStack) return () => {};

    let frame = 0;
    let destroyed = false;
    let active = !('IntersectionObserver' in window);
    let observer = null;
    let trackDistance = 0;
    let rollDistance = 0;
    let featureStart = 176;
    let featureEnd = 672;

    function enabled() {
        return desktop.matches && !reduced.matches;
    }

    function measure() {
        trackDistance = Math.max(0, track.scrollWidth - window.innerWidth);
        rollDistance = Math.max(
            0,
            rollStack.scrollHeight - rollWindow.clientHeight,
        );
        featureStart = clamp(window.innerWidth * .13, 176, 224);
        featureEnd = Math.min(
            window.innerWidth * .42,
            window.innerHeight * .82 * (16 / 9),
            832,
        );
    }

    function paint(progress) {
        const opening = phase(progress, 0.01, 0.2);
        const copy = phase(opening, 0.48, 0.92);
        const horizontalIn = phase(progress, 0.22, 0.3);
        const horizontal = phase(progress, 0.28, 0.62);
        const horizontalOut = phase(progress, 0.6, 0.68);
        const rollIn = phase(progress, 0.64, 0.72);
        const roll = phase(progress, 0.69, 0.9);
        const final = phase(progress, 0.88, 0.96);

        setNumber(root, '--article-opening', opening);
        setNumber(root, '--article-copy-opacity', copy);
        root.style.setProperty(
            '--article-copy-blur',
            `${((1 - copy) * 14).toFixed(2)}px`,
        );
        root.style.setProperty(
            '--article-copy-y',
            `${((1 - copy) * 20).toFixed(2)}px`,
        );
        root.style.setProperty(
            '--article-feature-width',
            `${(featureStart + (featureEnd - featureStart) * opening).toFixed(2)}px`,
        );
        setNumber(root, '--article-opening-exit', horizontalIn);
        setNumber(
            root,
            '--article-horizontal-opacity',
            horizontalIn * (1 - horizontalOut),
        );
        root.style.setProperty(
            '--article-track-x',
            `${(-trackDistance * horizontal).toFixed(2)}px`,
        );
        setNumber(root, '--article-roll-opacity', rollIn);
        root.style.setProperty(
            '--article-roll-y',
            `${(-rollDistance * roll).toFixed(2)}px`,
        );
        const hasCtaFocus = cta?.contains(document.activeElement) ?? false;
        const visibleFinal = hasCtaFocus ? Math.max(final, 0.82) : final;
        setNumber(root, '--article-final', visibleFinal);
        root.style.setProperty(
            '--article-cta-y',
            `${((1 - visibleFinal) * 24).toFixed(2)}px`,
        );
        const ctaReady = final > 0.82 || hasCtaFocus;
        root.classList.toggle('is-article-cta-ready', ctaReady);
        if (cta) cta.tabIndex = ctaReady ? 0 : -1;
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

    function syncMode() {
        root.classList.toggle('is-article-story-ready', enabled());
        if (!enabled()) {
            if (frame) window.cancelAnimationFrame(frame);
            frame = 0;
            root.classList.remove('is-article-cta-ready');
            if (cta) cta.removeAttribute('tabindex');
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
            rootMargin: '80% 0px 80% 0px',
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
    };
}

const articleStory = document.querySelector('[data-article-story]');
if (articleStory) mountArticleStory(articleStory);
