import { VIDEO_LABELS, safePlay, syncVideoTimeline } from './video-utilities.js';
import { initVisionVideoPreviews } from './video-previews.js';
import { createVideoFullscreenController } from './video-fullscreen.js';
export function initAboutVideoModal() {
    const triggers = Array.from(document.querySelectorAll('[data-vision-video-open]'));
    const modal = document.querySelector('[data-about-video-modal]');
    const shell = document.querySelector('[data-about-video-shell]');
    const closeButton = document.querySelector('[data-about-video-close]');
    const player = document.querySelector('[data-about-video-player]');
    const toggleButton = document.querySelector('[data-about-video-toggle]');
    const toggleIcon = document.querySelector('[data-about-video-toggle-icon]');
    const seek = document.querySelector('[data-about-video-seek]');
    const currentTimeLabel = document.querySelector('[data-about-video-current]');
    const durationLabel = document.querySelector('[data-about-video-duration]');
    const muteButton = document.querySelector('[data-about-video-mute]');
    const muteIcon = document.querySelector('[data-about-video-mute-icon]');
    const volume = document.querySelector('[data-about-video-volume]');
    const fullscreenButton = document.querySelector('[data-about-video-fullscreen]');
    const previewController = initVisionVideoPreviews();

    if (
        !triggers.length ||
        !modal ||
        !shell ||
        !closeButton ||
        !player ||
        !toggleButton ||
        !toggleIcon ||
        !seek ||
        !currentTimeLabel ||
        !durationLabel ||
        !muteButton ||
        !muteIcon ||
        !volume ||
        !fullscreenButton
    ) {
        return;
    }

    const locale = document.documentElement.lang?.split('-')[0] || 'en';
    const labels = VIDEO_LABELS[locale] ?? VIDEO_LABELS.en;
    const defaultSource = player.dataset.aboutVideoSrc || '';
    let activeSource = defaultSource;
    let hydratedSource = '';

    seek.setAttribute('aria-label', labels.timeline);
    volume.setAttribute('aria-label', labels.volume);

    function hydratePlayer(source = activeSource) {
        if (!source || hydratedSource === source) return;
        player.pause();
        hydratedSource = source;
        player.src = source;
        player.load();
        updateTimeline();
    }

    const { isShellFullscreen, updateFullscreenState, toggleFullscreen, exitFullscreen } =
        createVideoFullscreenController(shell, fullscreenButton, labels);

    function updatePlaybackState() {
        const paused = player.paused || player.ended;
        toggleButton.setAttribute('aria-label', paused ? labels.play : labels.pause);
        toggleIcon.textContent = paused ? '▶' : '❚❚';
    }

    function updateMuteState() {
        const muted = player.muted || player.volume === 0;
        muteButton.setAttribute('aria-label', muted ? labels.unmute : labels.mute);
        muteIcon.textContent = muted ? '🔇' : '🔊';
    }

    function updateTimeline() {
        syncVideoTimeline(player, seek, currentTimeLabel, durationLabel);
    }

    function togglePlayback() {
        hydratePlayer();

        if (player.paused || player.ended) {
            safePlay(player);
        } else {
            player.pause();
        }
    }

    function toggleMute() {
        if (player.muted || player.volume === 0) {
            if (player.volume === 0) {
                player.volume = 0.6;
                volume.value = '0.6';
            }
            player.muted = false;
        } else {
            player.muted = true;
        }

        updateMuteState();
    }

    async function closeModal() {
        if (isShellFullscreen()) {
            const attempt = exitFullscreen();
            if (attempt && typeof attempt.then === 'function') {
                try {
                    await attempt;
                } catch {
                    // The modal still needs to close even if fullscreen exit is rejected.
                }
            }
        }

        if (typeof modal.close === 'function' && modal.open) {
            modal.close();
        } else {
            modal.removeAttribute('open');
            player.pause();
            player.muted = true;
            previewController.resume();
        }
    }

    function openModal(trigger) {
        activeSource = trigger.dataset.visionVideoSrc || defaultSource;
        const label = trigger.dataset.visionVideoLabel || '';
        if (label) modal.setAttribute('aria-label', `${label} video`);

        if (typeof modal.showModal === 'function') {
            if (!modal.open) modal.showModal();
        } else {
            modal.setAttribute('open', '');
        }

        previewController.pause();
        hydratePlayer(activeSource);
        player.muted = false;
        volume.value = String(player.volume || 1);
        updateMuteState();
        safePlay(player);
    }

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => openModal(trigger));
    });
    closeButton.addEventListener('click', closeModal);
    toggleButton.addEventListener('click', togglePlayback);
    muteButton.addEventListener('click', toggleMute);
    fullscreenButton.addEventListener('click', toggleFullscreen);

    player.addEventListener('click', togglePlayback);
    player.addEventListener('dblclick', toggleFullscreen);
    player.addEventListener('play', updatePlaybackState);
    player.addEventListener('pause', updatePlaybackState);
    player.addEventListener('ended', updatePlaybackState);
    player.addEventListener('loadedmetadata', updateTimeline);
    player.addEventListener('durationchange', updateTimeline);
    player.addEventListener('timeupdate', updateTimeline);
    player.addEventListener('volumechange', updateMuteState);

    seek.addEventListener('input', () => {
        if (!Number.isFinite(player.duration) || player.duration <= 0) return;

        player.currentTime = (Number(seek.value) / 1000) * player.duration;
        updateTimeline();
    });

    volume.addEventListener('input', () => {
        player.volume = Number(volume.value);
        player.muted = player.volume === 0;
        updateMuteState();
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    modal.addEventListener('close', () => {
        player.pause();
        player.muted = true;
        previewController.resume();
    });

    document.addEventListener('fullscreenchange', updateFullscreenState);
    document.addEventListener('webkitfullscreenchange', updateFullscreenState);

    updatePlaybackState();
    updateMuteState();
    updateTimeline();
    updateFullscreenState();
}

function bootAboutVideoModal() {
    initAboutVideoModal();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootAboutVideoModal, { once: true });
} else {
    bootAboutVideoModal();
}
