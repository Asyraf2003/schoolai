export function armGalleryPattern(section) {
    const activate = () => section.classList.add('is-gallery-pattern-ready');

    if (!('IntersectionObserver' in window)) {
        activate();
        return () => {};
    }

    const observer = new IntersectionObserver((entries) => {
        if (!entries.some((entry) => entry.isIntersecting)) return;
        activate();
        observer.disconnect();
    }, {
        rootMargin: '75% 0px 75% 0px',
        threshold: 0,
    });

    observer.observe(section);
    return () => observer.disconnect();
}
