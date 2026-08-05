const clamp = (value) => Math.max(0, Math.min(1, value));
const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

function documentTop(element) {
    let top = 0;
    let current = element;

    while (current) {
        top += current.offsetTop;
        current = current.offsetParent;
    }

    return top;
}

function mountVisionImageMotion() {
    const root = document.querySelector('[data-vision-story]');
    const frameElement = root?.querySelector('[data-vision-image-frame]');
    const stack = root?.querySelector('[data-vision-image-stack]');

    if (!root || !frameElement || !stack || motionQuery.matches) return null;

    let observer = null;
    let animationFrame = null;
    let resizeTimer = null;
    let near = false;
    let destroyed = false;
    let start = 0;
    let distance = 1;
    let targetProgress = 0;
    let renderedProgress = 0;
    let lastFrameTime = performance.now();

    function calculateTarget() {
        targetProgress = clamp((window.scrollY - start) / distance);
    }

    function measure() {
        const frameTop = documentTop(frameElement);
        const frameCenter = frameTop + (frameElement.offsetHeight / 2);
        start = frameTop - window.innerHeight;
        distance = Math.max(
            1,
            frameCenter - (window.innerHeight / 2) - start,
        );
        calculateTarget();
    }

    function render() {
        stack.style.transform = `translate3d(0, ${-50 * renderedProgress}%, 0)`;
    }

    function tick(now) {
        animationFrame = null;
        if (destroyed || !near) return;

        const elapsed = Math.min(64, Math.max(1, now - lastFrameTime));
        const alpha = 1 - Math.exp(-elapsed / 88);
        renderedProgress += (targetProgress - renderedProgress) * alpha;
        lastFrameTime = now;
        render();

        if (Math.abs(targetProgress - renderedProgress) > 0.00015) {
            animationFrame = requestAnimationFrame(tick);
            return;
        }

        renderedProgress = targetProgress;
        render();
    }

    function scheduleFrame() {
        if (!near || animationFrame !== null) return;
        lastFrameTime = performance.now();
        animationFrame = requestAnimationFrame(tick);
    }

    function onScroll() {
        if (destroyed || !near) return;
        calculateTarget();
        scheduleFrame();
    }

    function rebuild() {
        if (destroyed) return;
        measure();
        renderedProgress = targetProgress;
        render();
    }

    function onResize() {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(rebuild, 140);
    }

    function onIntersection(entries) {
        near = entries.some((entry) => entry.isIntersecting);
        root.classList.toggle('is-image-near', near);

        if (near) {
            measure();
            scheduleFrame();
        } else if (animationFrame !== null) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
    }

    function onPageShow(event) {
        if (event.persisted) rebuild();
    }

    function destroy() {
        if (destroyed) return;
        destroyed = true;
        window.clearTimeout(resizeTimer);
        if (animationFrame !== null) cancelAnimationFrame(animationFrame);
        if (observer) observer.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', onPageShow);
        root.classList.remove('is-image-enhanced', 'is-image-near');
        stack.style.removeProperty('transform');
    }

    function onPageHide(event) {
        if (animationFrame !== null) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
        if (!event.persisted) destroy();
    }

    root.classList.add('is-image-enhanced');
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', onPageShow, { passive: true });
    window.addEventListener('pagehide', onPageHide, { passive: true });

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '100% 0px 100% 0px',
            threshold: 0,
        });
        observer.observe(root);
    } else {
        near = true;
        root.classList.add('is-image-near');
    }

    requestAnimationFrame(rebuild);
    if (document.fonts?.ready) {
        document.fonts.ready.then(rebuild).catch(() => {});
    }

    return { destroy };
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountVisionImageMotion, {
        once: true,
    });
} else {
    mountVisionImageMotion();
}
