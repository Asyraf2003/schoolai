const VIDEO_LABELS = Object.freeze({
    id: {
        play: 'Putar video',
        pause: 'Jeda video',
        mute: 'Bisukan video',
        unmute: 'Aktifkan suara',
        timeline: 'Linimasa video',
        volume: 'Volume video',
        enterFullscreen: 'Layar penuh',
        exitFullscreen: 'Keluar dari layar penuh',
    },
    ar: {
        play: 'تشغيل الفيديو',
        pause: 'إيقاف الفيديو مؤقتًا',
        mute: 'كتم صوت الفيديو',
        unmute: 'تشغيل صوت الفيديو',
        timeline: 'المخطط الزمني للفيديو',
        volume: 'مستوى صوت الفيديو',
        enterFullscreen: 'ملء الشاشة',
        exitFullscreen: 'الخروج من ملء الشاشة',
    },
    en: {
        play: 'Play video',
        pause: 'Pause video',
        mute: 'Mute video',
        unmute: 'Unmute video',
        timeline: 'Video timeline',
        volume: 'Video volume',
        enterFullscreen: 'Enter fullscreen',
        exitFullscreen: 'Exit fullscreen',
    },
});

function safePlay(video) {
    const attempt = video.play();
    if (attempt && typeof attempt.catch === 'function') {
        attempt.catch(() => {});
    }
}

function formatTime(seconds) {
    if (!Number.isFinite(seconds) || seconds < 0) return '0:00';

    const wholeSeconds = Math.floor(seconds);
    const minutes = Math.floor(wholeSeconds / 60);
    const remainder = wholeSeconds % 60;

    return `${minutes}:${String(remainder).padStart(2, '0')}`;
}

function activeFullscreenElement() {
    return document.fullscreenElement ?? document.webkitFullscreenElement ?? null;
}

function initVisionVideoPreviews() {
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

    function isShellFullscreen() {
        return activeFullscreenElement() === shell;
    }

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
        const duration = Number.isFinite(player.duration) ? player.duration : 0;
        const current = Number.isFinite(player.currentTime) ? player.currentTime : 0;
        const progress = duration > 0 ? Math.round((current / duration) * 1000) : 0;

        seek.value = String(Math.min(1000, Math.max(0, progress)));
        currentTimeLabel.textContent = formatTime(current);
        durationLabel.textContent = formatTime(duration);
    }

    function updateFullscreenState() {
        fullscreenButton.setAttribute(
            'aria-label',
            isShellFullscreen() ? labels.exitFullscreen : labels.enterFullscreen,
        );
        shell.classList.toggle('is-fullscreen', isShellFullscreen());
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

    function requestShellFullscreen() {
        const request = shell.requestFullscreen ?? shell.webkitRequestFullscreen;
        if (typeof request !== 'function') return;

        const attempt = request.call(shell);
        if (attempt && typeof attempt.catch === 'function') {
            attempt.catch(() => {});
        }
    }

    function exitFullscreen() {
        const exit = document.exitFullscreen ?? document.webkitExitFullscreen;
        if (typeof exit !== 'function') return null;

        try {
            return exit.call(document);
        } catch {
            return null;
        }
    }

    function toggleFullscreen() {
        if (isShellFullscreen()) {
            const attempt = exitFullscreen();
            if (attempt && typeof attempt.catch === 'function') {
                attempt.catch(() => {});
            }
            return;
        }

        requestShellFullscreen();
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
