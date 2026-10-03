export const createJourneyReleaseState = (root) => {
    let released = false;
    let lastScrollY = window.scrollY;

    const setReleased = (next) => {
        released = Boolean(next);
        root.classList.toggle('is-journey-released', released);
    };

    const onScroll = () => {
        const currentScrollY = window.scrollY;
        const scrollingUp = currentScrollY < lastScrollY;
        lastScrollY = currentScrollY;
        if (!released || !scrollingUp) return;

        const rect = root.getBoundingClientRect();
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const intersectsViewport = rect.bottom > 0 && rect.top < viewportHeight;
        if (intersectsViewport) setReleased(false);
    };

    window.addEventListener('scroll', onScroll, { passive: true });

    return {
        isReleased: () => released,
        reset: () => setReleased(false),
        setReleased,
    };
};
