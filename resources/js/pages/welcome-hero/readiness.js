export const HERO_READY_EVENT = 'schoolai:hero-ready';
const HERO_READY_FALLBACK_MS = 5000;

function signalHeroReady(root, reason) {
    if (root.dataset.heroReady === 'true') return;
    root.dataset.heroReady = 'true';
    document.documentElement.dataset.heroReady = 'true';
    window.dispatchEvent(new CustomEvent(HERO_READY_EVENT, {
        detail: { reason },
    }));
}

export function armHeroReadySignal(root, slide) {
    let settled = false;
    let fallbackTimer = 0;

    const finish = (reason) => {
        if (settled) return;
        settled = true;
        if (fallbackTimer) window.clearTimeout(fallbackTimer);
        signalHeroReady(root, reason);
    };

    fallbackTimer = window.setTimeout(
        () => finish('fallback-timeout'),
        HERO_READY_FALLBACK_MS,
    );

    const video = slide?.querySelector('[data-hero-video]');
    if (video) {
        if (!video.paused && video.readyState >= 2) {
            finish('video-playing');
            return;
        }
        video.addEventListener('playing', () => finish('video-playing'), { once: true });
        video.addEventListener('error', () => finish('video-error'), { once: true });
        return;
    }

    const image = slide?.querySelector('img');
    if (!image) {
        finish('no-media');
        return;
    }

    if (image.complete && image.naturalWidth > 0) {
        finish('image-ready');
        return;
    }

    image.addEventListener('load', () => finish('image-ready'), { once: true });
    image.addEventListener('error', () => finish('image-error'), { once: true });
}
