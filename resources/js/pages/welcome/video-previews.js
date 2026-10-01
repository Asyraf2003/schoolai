import { safePlay } from './video-utilities.js';

export function initVisionVideoPreviews() {
    const previews = Array.from(document.querySelectorAll('[data-vision-video-preview]'));
    const activePreviews = new Set();
    if (!previews.length) return { pause: () => {}, resume: () => {} };

    function hydrate(preview) {
        if (preview.dataset.visionVideoHydrated === 'true') return;
        const source = preview.dataset.visionVideoSrc || '';
        if (!source) return;

        preview.dataset.visionVideoHydrated = 'true';
        preview.muted = true;
        preview.defaultMuted = true;
        preview.loop = true;
        preview.playsInline = true;
        preview.src = source;
        preview.load();
    }

    function play(preview) {
        hydrate(preview);
        if (!document.hidden) safePlay(preview);
    }

    previews.forEach((preview) => {
        preview.addEventListener('loadeddata', () => {
            preview.classList.add('is-ready');
        }, { once: true });
    });

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const preview = entry.target;
                if (entry.isIntersecting) {
                    activePreviews.add(preview);
                    play(preview);
                } else {
                    activePreviews.delete(preview);
                    preview.pause();
                }
            });
        }, {
            rootMargin: '180px 0px',
            threshold: 0.01,
        });

        previews.forEach((preview) => observer.observe(preview));
    } else {
        previews.forEach((preview) => {
            activePreviews.add(preview);
            play(preview);
        });
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            previews.forEach((preview) => preview.pause());
            return;
        }

        activePreviews.forEach(play);
    });

    return {
        pause() {
            previews.forEach((preview) => preview.pause());
        },
        resume() {
            activePreviews.forEach(play);
        },
    };
}

