function safePlay(video) {
    const attempt = video.play();
    if (attempt && typeof attempt.catch === 'function') attempt.catch(() => {});
}

export function mountGalleryVideoPreviews(root) {
    const previews = Array.from(root.querySelectorAll('[data-gallery-video-preview]'));
    const activePreviews = new Set();
    let observer = null;

    if (!previews.length) return () => {};

    function hydrate(preview) {
        if (preview.dataset.galleryVideoHydrated === 'true') return;
        const source = preview.dataset.galleryVideoSrc || '';
        if (!source) return;

        preview.dataset.galleryVideoHydrated = 'true';
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
        observer = new IntersectionObserver((entries) => {
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
        }, { rootMargin: '180px 0px', threshold: 0.01 });
        previews.forEach((preview) => observer.observe(preview));
    } else {
        previews.forEach((preview) => {
            activePreviews.add(preview);
            play(preview);
        });
    }

    function onVisibilityChange() {
        if (document.hidden) previews.forEach((preview) => preview.pause());
        else activePreviews.forEach(play);
    }

    document.addEventListener('visibilitychange', onVisibilityChange);

    return () => {
        observer?.disconnect();
        activePreviews.clear();
        previews.forEach((preview) => preview.pause());
        document.removeEventListener('visibilitychange', onVisibilityChange);
    };
}
