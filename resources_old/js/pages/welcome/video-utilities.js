export const VIDEO_LABELS = Object.freeze({
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

export function safePlay(video) {
    const attempt = video.play();
    if (attempt && typeof attempt.catch === 'function') {
        attempt.catch(() => {});
    }
}

export function formatTime(seconds) {
    if (!Number.isFinite(seconds) || seconds < 0) return '0:00';

    const wholeSeconds = Math.floor(seconds);
    const minutes = Math.floor(wholeSeconds / 60);
    const remainder = wholeSeconds % 60;

    return `${minutes}:${String(remainder).padStart(2, '0')}`;
}

export function syncVideoTimeline(player, seek, currentTimeLabel, durationLabel) {
    const duration = Number.isFinite(player.duration) ? player.duration : 0;
    const current = Number.isFinite(player.currentTime) ? player.currentTime : 0;
    const progress = duration > 0 ? Math.round((current / duration) * 1000) : 0;

    seek.value = String(Math.min(1000, Math.max(0, progress)));
    currentTimeLabel.textContent = formatTime(current);
    durationLabel.textContent = formatTime(duration);
}

