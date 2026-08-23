const TRANSITION_KEY = 'schoolai:gallery-route-transition';

export function bindGalleryRouteExit(root, getEngine) {
    const link = root.querySelector('[data-depth-gallery-end-link]');
    const viewport = root.querySelector('[data-depth-gallery-viewport]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let leaving = false;
    let animations = [];

    if (!link || !viewport) {
        return { destroy() {}, restore() {} };
    }

    const onClick = (event) => {
        if (!isPlainPrimaryClick(event)) return;
        if (!root.classList.contains('is-depth-active')) return;
        if (!root.classList.contains('is-depth-end-ready')) return;
        if (reducedMotion.matches || typeof link.animate !== 'function') return;

        event.preventDefault();
        if (leaving) return;
        leaving = true;
        getEngine()?.stop();
        root.classList.add('is-depth-leaving');
        rememberTransition();

        try {
            const timing = {
                duration: 760,
                easing: 'cubic-bezier(0.76, 0, 0.24, 1)',
                fill: 'forwards',
            };
            const viewportAnimation = viewport.animate([
                {
                    transform: 'scale(1)',
                    filter: 'blur(0px)',
                    opacity: 1,
                },
                {
                    transform: 'scale(1.16)',
                    filter: 'blur(18px)',
                    opacity: 0.08,
                },
            ], timing);
            const linkAnimation = link.animate([
                {
                    transform: 'translate3d(0, 0, 0) rotate(0deg) scale(1)',
                    filter: 'blur(0px)',
                    opacity: 1,
                },
                {
                    offset: 0.68,
                    transform: 'translate3d(0, 0, 0) rotate(0deg) scale(1.55)',
                    filter: 'blur(0px)',
                    opacity: 1,
                },
                {
                    transform: 'translate3d(0, 0, 0) rotate(45deg) scale(2.05)',
                    filter: 'blur(14px)',
                    opacity: 0,
                },
            ], timing);

            animations = [viewportAnimation, linkAnimation];
            Promise.allSettled([
                viewportAnimation.finished,
                linkAnimation.finished,
            ]).then(() => {
                if (leaving) navigate(link.href);
            });
        } catch (error) {
            console.warn('Gallery route transition failed', error);
            navigate(link.href);
        }
    };

    link.addEventListener('click', onClick);

    const restore = () => {
        leaving = false;
        animations.forEach((animation) => animation.cancel());
        animations = [];
        root.classList.remove('is-depth-leaving');
    };

    return {
        restore,
        destroy() {
            restore();
            link.removeEventListener('click', onClick);
        },
    };
}

export function playGalleryRouteArrival() {
    if (!consumeTransition()) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const main = document.querySelector('#main-content');
    if (!main || typeof main.animate !== 'function') return;

    main.animate([
        {
            transform: 'scale(1.035)',
            filter: 'blur(18px)',
            opacity: 0.12,
        },
        {
            transform: 'scale(1)',
            filter: 'blur(0px)',
            opacity: 1,
        },
    ], {
        duration: 560,
        easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
        fill: 'both',
    });
}

function isPlainPrimaryClick(event) {
    return !event.defaultPrevented
        && event.button === 0
        && !event.metaKey
        && !event.ctrlKey
        && !event.shiftKey
        && !event.altKey;
}

function navigate(href) {
    window.location.assign(href);
}

function rememberTransition() {
    try {
        window.sessionStorage.setItem(TRANSITION_KEY, '1');
    } catch {
        // Storage is optional; navigation still completes.
    }
}

function consumeTransition() {
    try {
        const active = window.sessionStorage.getItem(TRANSITION_KEY) === '1';
        window.sessionStorage.removeItem(TRANSITION_KEY);
        return active;
    } catch {
        return false;
    }
}
