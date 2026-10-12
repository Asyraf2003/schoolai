// Existing scroll-pacing is the only wheel owner.
// Blue-stage fill follows raw input; release drains at half its measured rise speed.
export const FOOTER_GESTURE_GAP_MS = 280;
export const FOOTER_GESTURE_MIN_PX = 14;
export const FOOTER_PROGRESS_DISTANCE_PX = 840;
export const FOOTER_IDLE_DELAY_MS = 120;
export const FOOTER_MIN_RISE_MS = 100;
const EPS = 1;
const clamp = v => Math.max(0, Math.min(1, v));

export function createFooterGate(bounds, navigate) {
    let phase = 'to-white', lastWheel = null, lastTick = null;
    let charge = 0, blueUnlocked = false, completed = false;
    let riseStart = null, riseAmount = 0, decayPerMs = 0;
    const limits = () => {
        const { white, blue } = bounds();
        return { white, blue: Math.max(white, blue) };
    };
    const clearCharge = () => {
        charge = 0; blueUnlocked = false; completed = false; lastTick = null;
        riseStart = null; riseAmount = 0; decayPerMs = 0;
    };
    const within = (position, stop) => position >= stop - EPS;
    const settle = now => {
        if (phase !== 'blue-held' || charge <= 0) { lastTick = now; return false; }
        const start = Math.max(lastTick ?? now, (lastWheel ?? now) + FOOTER_IDLE_DELAY_MS);
        const elapsed = Math.max(0, now - start);
        const before = charge;
        charge = clamp(charge - elapsed * decayPerMs);
        lastTick = now;
        return before !== charge;
    };
    const resetFromReverse = position => {
        const { white, blue } = limits();
        if (position < white - 8) phase = 'to-white';
        else if (position < blue - 8 && phase === 'blue-held') phase = 'to-blue';
    };
    const wheel = (delta, position, now) => {
        if (!Number.isFinite(delta) || !Number.isFinite(now) || delta === 0) return false;
        settle(now);
        const fresh = lastWheel === null || now - lastWheel >= FOOTER_GESTURE_GAP_MS;
        const newRise = lastWheel === null || now - lastWheel >= FOOTER_IDLE_DELAY_MS;
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
            if (!blueUnlocked && !fresh) return true;
            if (!blueUnlocked) blueUnlocked = true;
            if (newRise || riseStart === null || charge === 0) { riseStart = now; riseAmount = 0; }
            const added = Math.min(1 - charge, delta / FOOTER_PROGRESS_DISTANCE_PX);
            riseAmount += added;
            charge = clamp(charge + added);
            decayPerMs = riseAmount / (2 * Math.max(FOOTER_MIN_RISE_MS, now - riseStart));
            if (charge >= 1 && !completed) { completed = true; navigate(); }
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
