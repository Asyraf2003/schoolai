import {
    DESKTOP_QUERY,
    REDUCED_MOTION_QUERY,
    TRACK_SELECTOR,
    addMediaListener,
    clamp,
    removeMediaListener
} from './core.js';

export function createReelMotion(root, callbacks) {
    var track = root.querySelector(TRACK_SELECTOR);
    var desktopMedia = window.matchMedia(DESKTOP_QUERY);
    var reducedMotionMedia = window.matchMedia(REDUCED_MOTION_QUERY);
    var frame = null;
    var observer = null;
    var enhanced = false;
    var near = false;
    var destroyed = false;

    function canEnhance() {
        return Boolean(
            track &&
            desktopMedia.matches &&
            !reducedMotionMedia.matches &&
            typeof window.IntersectionObserver === 'function' &&
            typeof window.requestAnimationFrame === 'function' &&
            window.CSS &&
            window.CSS.supports('position', 'sticky')
        );
    }

    function publishProximity(nextNear) {
        if (near === nextNear) return;
        near = nextNear;
        callbacks.onProximityChange(near);
    }

    function renderProgress() {
        frame = null;
        if (!enhanced || destroyed) return;

        var viewportHeight = Math.max(
            window.innerHeight || document.documentElement.clientHeight,
            1
        );
        var rect = track.getBoundingClientRect();
        var range = Math.max(track.offsetHeight - viewportHeight, 1);
        var progress = clamp(-rect.top / range, 0, 1);

        root.style.setProperty('--about-progress', progress.toFixed(4));
        callbacks.onProgress(progress);
    }

    function requestRender() {
        if (!enhanced || !near || frame !== null) return;
        frame = window.requestAnimationFrame(renderProgress);
    }

    function cancelRender() {
        if (frame === null) return;
        window.cancelAnimationFrame(frame);
        frame = null;
    }

    function onIntersection(entries) {
        var entry = entries[entries.length - 1];
        publishProximity(Boolean(entry && entry.isIntersecting));
        if (near) renderProgress();
        else cancelRender();
    }

    function enable() {
        enhanced = true;
        root.classList.add('about-reel--enhanced');
        callbacks.onModeChange(true);
        observer = new IntersectionObserver(onIntersection, { rootMargin: '65% 0px' });
        observer.observe(track);
        window.addEventListener('scroll', requestRender, { passive: true });
        window.addEventListener('resize', requestRender, { passive: true });
        renderProgress();
    }

    function disable() {
        cancelRender();
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', requestRender);
        if (observer) {
            observer.disconnect();
            observer = null;
        }
        enhanced = false;
        publishProximity(false);
        callbacks.onModeChange(false);
        callbacks.onProgress(0);
        root.classList.remove('about-reel--enhanced');
        root.style.setProperty('--about-progress', '0');
    }

    function synchronizeMode() {
        if (destroyed) return;
        var nextEnhanced = canEnhance();
        if (nextEnhanced === enhanced) {
            if (enhanced) renderProgress();
            return;
        }
        disable();
        if (nextEnhanced) enable();
    }

    function destroy() {
        if (destroyed) return;
        disable();
        destroyed = true;
        removeMediaListener(desktopMedia, synchronizeMode);
        removeMediaListener(reducedMotionMedia, synchronizeMode);
    }

    addMediaListener(desktopMedia, synchronizeMode);
    addMediaListener(reducedMotionMedia, synchronizeMode);
    synchronizeMode();
    return { destroy: destroy };
}
