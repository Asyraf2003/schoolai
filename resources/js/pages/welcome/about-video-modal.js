const PREVIEW_ROOT_MARGIN = '100% 0px 100% 0px';
const DEFAULT_PREVIEW_START = 30;
const DEFAULT_PREVIEW_END = 50;

function numericData(value, fallback) {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

function safePlay(video) {
    const attempt = video.play();
    if (attempt && typeof attempt.catch === 'function') {
        attempt.catch(() => {});
    }
}

export function initAboutVideoModal() {
    const trigger = document.querySelector('[data-about-video-open]');
    const modal = document.querySelector('[data-about-video-modal]');
    const closeButton = document.querySelector('[data-about-video-close]');
    const preview = document.querySelector('[data-about-video-preview]');
    const player = document.querySelector('[data-about-video-player]');

    if (!trigger || !modal || !closeButton || !preview || !player) return;

    const previewSource = preview.dataset.aboutVideoSrc || '';
    const playerSource = player.dataset.aboutVideoSrc || '';
    const previewStart = numericData(
        preview.dataset.aboutPreviewStart,
        DEFAULT_PREVIEW_START,
    );
    const previewEnd = numericData(
        preview.dataset.aboutPreviewEnd,
        DEFAULT_PREVIEW_END,
    );
    let previewHydrated = false;
    let playerHydrated = false;
    let previewObserver = null;

    function boundedPreviewStart() {
        if (!Number.isFinite(preview.duration)) return previewStart;
        return Math.min(previewStart, Math.max(0, preview.duration - 0.25));
    }

    function boundedPreviewEnd() {
        if (!Number.isFinite(preview.duration)) return previewEnd;
        return Math.min(previewEnd, preview.duration);
    }

    function seekPreviewStart() {
        try {
            preview.currentTime = boundedPreviewStart();
        } catch (error) {
            // Safari can reject a seek until metadata is ready.
        }
    }

    function hydratePreview() {
        if (previewHydrated || previewSource === '') return;
        previewHydrated = true;
        preview.src = previewSource;
        preview.load();
    }

    function hydratePlayer() {
        if (playerHydrated || playerSource === '') return;
        playerHydrated = true;
        player.src = playerSource;
        player.load();
    }

    function closeModal() {
        if (typeof modal.close === 'function' && modal.open) {
            modal.close();
        } else {
            modal.removeAttribute('open');
        }
    }

    function openModal() {
        if (typeof modal.showModal === 'function') {
            if (!modal.open) modal.showModal();
        } else {
            modal.setAttribute('open', '');
        }

        hydratePlayer();
        player.muted = false;
        safePlay(player);
    }

    preview.addEventListener('loadedmetadata', () => {
        seekPreviewStart();
        safePlay(preview);
    });

    preview.addEventListener('timeupdate', () => {
        const end = boundedPreviewEnd();
        if (end <= boundedPreviewStart()) return;
        if (preview.currentTime >= end - 0.05) {
            seekPreviewStart();
            safePlay(preview);
        }
    });

    if ('IntersectionObserver' in window) {
        previewObserver = new IntersectionObserver((entries) => {
            if (!entries.some((entry) => entry.isIntersecting)) return;
            hydratePreview();
            previewObserver?.disconnect();
            previewObserver = null;
        }, {
            rootMargin: PREVIEW_ROOT_MARGIN,
            threshold: 0.01,
        });
        previewObserver.observe(trigger);
    } else {
        hydratePreview();
    }

    trigger.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    modal.addEventListener('close', () => {
        player.pause();
        player.muted = true;
        if (previewHydrated) safePlay(preview);
    });
}

function bootAboutVideoModal() {
    initAboutVideoModal();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootAboutVideoModal, { once: true });
} else {
    bootAboutVideoModal();
}
