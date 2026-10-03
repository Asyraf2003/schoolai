export function mountValuesLifecycle(root, nodes, handlers) {
    const {
        onIntersection,
        onPageHide,
        onPageShow,
        onPreference,
        onResize,
        onScroll,
        onVisibility,
        reduced,
    } = handlers;
    let observer = null;
    let resizeObserver = null;

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', onPageShow);
    window.addEventListener('pagehide', onPageHide);
    document.addEventListener('visibilitychange', onVisibility);
    reduced.addEventListener('change', onPreference);

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '60% 0px 60% 0px',
            threshold: 0.01,
        });
        observer.observe(root);
    }
    if ('ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(onResize);
        [nodes.stage, nodes.grid, nodes.spatialHost]
            .forEach((node) => resizeObserver.observe(node));
    }

    return () => {
        observer?.disconnect();
        resizeObserver?.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', onPageShow);
        window.removeEventListener('pagehide', onPageHide);
        document.removeEventListener('visibilitychange', onVisibility);
        reduced.removeEventListener('change', onPreference);
    };
}
