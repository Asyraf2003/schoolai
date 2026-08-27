export function initAboutVideoModal() {
    const trigger = document.querySelector('[data-about-video-open]');
    const modal = document.querySelector('[data-about-video-modal]');
    const closeButton = document.querySelector('[data-about-video-close]');
    const preview = document.querySelector('[data-about-video-preview]');
    const player = document.querySelector('[data-about-video-player]');

    if (!trigger || !modal || !closeButton || !preview || !player) return;

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

        try {
            if (Number.isFinite(preview.currentTime)) {
                player.currentTime = preview.currentTime;
            }
        } catch (error) {
            // Metadata may not be available yet.
        }

        player.muted = false;
        const playAttempt = player.play();
        if (playAttempt && typeof playAttempt.catch === 'function') {
            playAttempt.catch(() => {});
        }
    }

    trigger.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    modal.addEventListener('close', () => {
        player.pause();
        player.muted = true;
        const previewPlay = preview.play();
        if (previewPlay && typeof previewPlay.catch === 'function') {
            previewPlay.catch(() => {});
        }
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
