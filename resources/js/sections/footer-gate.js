// Two scroll checkpoints; progression requires independent wheel gestures.
// This is a pure policy. The existing global scroll-pacing owner delivers inputs.
export const FOOTER_GESTURE_GAP_MS = 280;
export const FOOTER_GESTURE_MIN_PX = 14;
export const FOOTER_PROGRESS_PX = 840;
const EPS = 1;

export function createFooterGate(bounds, navigate) {
    let phase = 'to-white';
    let lastWheel = null;
    let charge = 0, charging = false;
    const clearCharge = () => { charge = 0; charging = false; };
    const limits = () => {
        const { white, blue } = bounds();
        return { white, blue: Math.max(white, blue) };
    };
    const within = (position, stop) => position >= stop - EPS;
    const resetFromReverse = position => {
        const { white, blue } = limits();
        if (position < white - 8) phase = 'to-white';
        else if (position < blue - 8 && phase === 'blue-held') phase = 'to-blue';
    };
    const wheel = (delta, position, now) => {
        const fresh = lastWheel === null || now - lastWheel >= FOOTER_GESTURE_GAP_MS;
        lastWheel = now;
        if (delta < 0) { clearCharge(); resetFromReverse(position); return false; }
        if (delta <= 0) return false;
        const { white, blue } = limits();
        if (phase === 'to-white' && within(position, white)) phase = 'white-held';
        if (phase === 'to-blue' && within(position, blue)) phase = 'blue-held';
        const intentional = fresh && delta >= FOOTER_GESTURE_MIN_PX;
        if (phase === 'white-held') {
            if (intentional) { phase = 'to-blue'; return false; }
            return true;
        }
        if (phase === 'blue-held') {
            if (!charging && intentional) charging = true;
            if (charging) {
                // Cap each packet, so one high-delta wheel event cannot redirect.
                charge = Math.min(FOOTER_PROGRESS_PX, charge + Math.min(delta, 140));
                if (charge >= FOOTER_PROGRESS_PX) navigate();
            }
            return true;
        }
        return false;
    };
    const limit = (requested, position) => {
        if (requested <= 0) return requested;
        if (phase === 'white-held' || phase === 'blue-held') return 0;
        const { white, blue } = limits();
        const stop = phase === 'to-white' ? white : blue;
        return Math.max(0, Math.min(requested, stop - position));
    };
    const advanced = position => {
        const { white, blue } = limits();
        if (phase === 'to-white' && within(position, white)) {
            phase = 'white-held';
            return true;
        }
        if (phase === 'to-blue' && within(position, blue)) {
            phase = 'blue-held';
            return true;
        }
        // Any reverse scroll below the white checkpoint must rearm white,
        // including when the blue checkpoint is already in "to-blue".
        if (position < white - 8) { phase = 'to-white'; clearCharge(); }
        else if (phase === 'blue-held' && position < blue - 8) { phase = 'to-blue'; clearCharge(); }
        return false;
    };
    const reset = position => {
        lastWheel = null;
        clearCharge();
        const { white, blue } = limits();
        phase = position >= blue - EPS ? 'blue-held'
            : position >= white - EPS ? 'white-held' : 'to-white';
    };
    return {
        wheel, limit, advanced, reset,
        get phase() { return phase; },
        get progress() { return charge / FOOTER_PROGRESS_PX; },
    };
}
