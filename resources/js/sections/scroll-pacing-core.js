export const SCROLL_DEBT_REFERENCE_PX = 224;
export const SCROLL_IDLE_CUTOFF_MS = 180;
const RESPONSE_MS = 60;

export const clampScroll = (value, lower, upper) => Math.max(lower, Math.min(upper, value));

export function wheelPixels(event, viewportHeight) {
    if (!Number.isFinite(event.deltaY)) return 0;
    const unit = event.deltaMode === 1 ? 18 : event.deltaMode === 2 ? viewportHeight : 1;
    return event.deltaY * unit;
}

export function createPacingState() {
    return { pending: 0, lastInput: 0, lastFrame: null, velocity: 0, deliveredInput: null };
}

export function queuePacingInput(state, delta, now) {
    if (!Number.isFinite(delta) || !delta) return false;
    const reverse = state.pending * delta < 0;
    if (reverse) { state.pending = 0; state.lastFrame = null; }
    // Larger intentional input remains faster in free space. Bound debt by
    // this impulse, never by one velocity ceiling for the entire document.
    const debtLimit = SCROLL_DEBT_REFERENCE_PX * Math.max(1, Math.sqrt(Math.abs(delta) / 112));
    state.pending = clampScroll(state.pending + delta * 2, -debtLimit, debtLimit);
    state.lastInput = now;
    return reverse;
}

// Distance and observation time belong to each choreography, not the document.
export function limitPacingTravel(step, elapsed, ranges, position) {
    const sign = Math.sign(step);
    let distance = Math.abs(step);
    for (const range of ranges) {
        const approach = sign > 0 ? range.start - position : position - range.end;
        const inside = position >= range.start && position <= range.end;
        if (!inside && (approach < 0 || approach >= distance)) continue;
        const timed = Number.isFinite(range.speed) ? range.speed * elapsed / 1000 : Infinity;
        const available = Math.min(timed, (range.end - range.start) / 14);
        distance = Math.min(distance, Math.max(0, approach) + available);
    }
    return sign * distance;
}

export function takePacingStep(state, now, ranges = [], position = 0) {
    if (!state.pending || now - state.lastInput > SCROLL_IDLE_CUTOFF_MS) {
        state.pending = 0; state.lastFrame = null;
        return 0;
    }
    const elapsed = state.lastFrame === null ? 1000 / 60
        : clampScroll(now - state.lastFrame, 0, 34);
    state.lastFrame = now;
    if (now - state.lastInput > 34) {
        const coast = Math.min(SCROLL_DEBT_REFERENCE_PX, Math.abs(state.velocity) * RESPONSE_MS / 1000);
        state.pending = Math.sign(state.pending) * Math.min(Math.abs(state.pending), coast);
    }
    let requested = state.pending * (1 - Math.exp(-elapsed / RESPONSE_MS));
    if (state.deliveredInput === state.lastInput) {
        // Old debt cannot accelerate the page when a protected phase ends.
        requested = Math.sign(requested) * Math.min(Math.abs(requested), Math.abs(state.velocity) * elapsed / 1000);
    }
    const step = limitPacingTravel(requested, elapsed, ranges, position);
    state.pending -= step;
    if (elapsed > 0 && step) { state.velocity = step * 1000 / elapsed; state.deliveredInput = state.lastInput; }
    if (Math.abs(state.pending) < .5) state.pending = 0;
    return step;
}
