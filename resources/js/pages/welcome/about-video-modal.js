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
    const player = document.querySelector('[data-about-video-player]');

    if (!trigger || !modal || !closeButton || !player) return;

    const playerSource = player.dataset.aboutVideoSrc || '';
    let playerHydrated = false;

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

    trigger.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    modal.addEventListener('close', () => {
        player.pause();
        player.muted = true;
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
