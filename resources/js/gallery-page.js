import { mountHeaderSound } from './sections/header-sound.js';
import { mountGalleryViewer } from './gallery-viewer.js';

const videos = [...document.querySelectorAll('[data-gallery-video]')];
const button = document.querySelector('[data-gallery-sound]');
const sound = mountHeaderSound(button);
const preference = matchMedia('(prefers-reduced-motion: reduce)');
const visible = new Set();
let enabled = false, suspended = false, viewing = false;
const status = () => {
    button.disabled = !videos.length || viewing;
    button.setAttribute('aria-pressed', String(enabled));
    button.querySelector('[data-audio-label]').textContent = enabled ? button.dataset.textOn : button.dataset.textOff;
    button.setAttribute('aria-label', enabled ? button.dataset.labelOn : button.dataset.labelOff);
    sound.update({ available: videos.length > 0, enabled }, { desktop: false, concealed: false, panel: null });
};
const pause = () => videos.forEach(video => { video.pause(); video.muted = true; });
const reconcile = () => {
    if (suspended || viewing || document.hidden) { pause(); return; }
    const active = [...visible].slice(0, 2);
    videos.forEach(video => {
        video.muted = !enabled || video !== active[0];
        if (!active.includes(video)) video.pause();
        else if (!preference.matches) video.play().catch(() => {});
    });
};
const observer = new IntersectionObserver(entries => {
    entries.forEach(({ target, isIntersecting, intersectionRatio }) => {
        if (isIntersecting && intersectionRatio >= .35) visible.add(target);
        else visible.delete(target);
    });
    reconcile();
}, { threshold: [0, .35] });
videos.forEach(video => {
    observer.observe(video);
    video.addEventListener('error', () => {
        if (!video.muted) { enabled = false; status(); }
    });
});
button.addEventListener('click', async () => {
    if (enabled) { enabled = false; videos.forEach(video => { video.muted = true; }); status(); return; }
    const video = [...visible][0] ?? videos[0];
    if (!video) return;
    if (!visible.has(video)) video.scrollIntoView({ block: 'center', behavior: 'instant' });
    video.muted = false;
    try { await video.play(); enabled = true; } catch { video.muted = true; enabled = false; }
    status();
});
mountGalleryViewer(open => { viewing = open; if (open) enabled = false; reconcile(); status(); });
const reveal = new IntersectionObserver(entries => entries.forEach(({ target, isIntersecting }) => {
    if (!isIntersecting) return;
    if (!preference.matches) target.dataset.revealed = '';
    reveal.unobserve(target);
}), { threshold: .08 });
document.querySelectorAll('[data-gallery-card]').forEach(card => reveal.observe(card));
preference.addEventListener('change', () => { pause(); reconcile(); });
document.addEventListener('visibilitychange', reconcile);
document.addEventListener('public:navigate', () => { suspended = true; pause(); });
window.addEventListener('pagehide', event => {
    suspended = true; pause();
    if (!event.persisted) { observer.disconnect(); reveal.disconnect(); sound.dispose(); }
});
window.addEventListener('pageshow', () => { suspended = false; enabled = false; reconcile(); status(); });
status();
