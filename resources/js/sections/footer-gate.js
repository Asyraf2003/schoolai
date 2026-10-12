// Existing scroll-pacing is the only wheel owner.
// Each separate blue-stage gesture charges at most 55% of the progress bar.
export const FOOTER_GESTURE_GAP_MS = 280;
export const FOOTER_GESTURE_MIN_PX = 14;
export const FOOTER_GESTURE_CHARGE_MAX = .55;
export const FOOTER_IDLE_DELAY_MS = 450;
export const FOOTER_IDLE_DECAY_PER_MS = .00032;
const EPS = 1;
const clamp = v => Math.max(0, Math.min(1, v));

export function createFooterGate(bounds, navigate) {
    let phase = 'to-white', lastWheel = null, lastTick = null;
    let charge = 0, gestureCharge = 0, blueUnlocked = false;
    const limits = () => {
        const { white, blue } = bounds();
        return { white, blue: Math.max(white, blue) };
    };
    const clearCharge = () => {
        charge = 0; gestureCharge = 0; blueUnlocked = false; lastTick = null;
    };
    const within = (position, stop) => position >= stop - EPS;
    const settle = now => {
        if (phase !== 'blue-held' || charge <= 0) { lastTick = now; return false; }
        const start = Math.max(lastTick ?? now, (lastWheel ?? now) + FOOTER_IDLE_DELAY_MS);
        const elapsed = Math.max(0, now - start);
        const before = charge;
        charge = clamp(charge - elapsed * FOOTER_IDLE_DECAY_PER_MS);
        lastTick = now;
        return before !== charge;
    };
    const resetFromReverse = position => {
        const { white, blue } = limits();
        if (position < white - 8) phase = 'to-white';
        else if (position < blue - 8 && phase === 'blue-held') phase = 'to-blue';
    };
    const wheel = (delta, position, now) => {
        settle(now);
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
            // Tail packets of the gesture that entered blue cannot charge it.
            if (!blueUnlocked && !intentional) return true;
            if (!blueUnlocked) blueUnlocked = true;
            if (fresh) gestureCharge = 0;
            if (delta >= FOOTER_GESTURE_MIN_PX) {
                const remaining = Math.max(0, FOOTER_GESTURE_CHARGE_MAX - gestureCharge);
                const added = Math.min(remaining, Math.max(.5, Math.min(.55, delta / 250)));
                gestureCharge += added;
                charge = clamp(charge + added);
                if (charge >= 1) navigate();
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
            phase = 'white-held'; return true;
        }
        if (phase === 'to-blue' && within(position, blue)) {
            phase = 'blue-held'; clearCharge(); return true;
        }
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
    return { wheel, limit, advanced, reset, settle,
        get phase() { return phase; },
        get progress() { return charge; },
    };
}
