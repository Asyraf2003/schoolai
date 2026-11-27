export const SCROLL_INPUT_GAIN = 0.85;
export const SCROLL_INERTIA_TAU_MS = 180;
export const SCROLL_IDLE_CUTOFF_MS = 1800;
export const SCROLL_MAX_PENDING_PX = 2400;

export const clampScroll = (value, lower, upper) => Math.max(lower, Math.min(upper, value));

export function wheelPixels(event, viewportHeight) {
    if (!Number.isFinite(event.deltaY)) return 0;
    const unit = event.deltaMode === 1 ? 18 : event.deltaMode === 2 ? viewportHeight : 1;
    return event.deltaY * unit;
}

export function createPacingState() {
    return { pending: 0, lastInput: 0, lastFrame: null, velocity: 0 };
}

export function queuePacingInput(state, delta, now) {
    if (!Number.isFinite(delta) || !delta) return false;
    const reverse = state.pending * delta < 0;
    if (reverse) {
        state.pending = 0;
        state.lastFrame = null;
        state.velocity = 0;
    }
    state.pending = clampScroll(
        state.pending + delta * SCROLL_INPUT_GAIN,
        -SCROLL_MAX_PENDING_PX,
        SCROLL_MAX_PENDING_PX,
    );
    state.lastInput = now;
    return reverse;
}

export function takePacingStep(state, now) {
    if (!state.pending || now - state.lastInput > SCROLL_IDLE_CUTOFF_MS) {
        state.pending = 0;
        state.lastFrame = null;
        state.velocity = 0;
        return 0;
    }
    if (state.lastFrame !== null && now - state.lastFrame > 120 && now - state.lastInput > 120) {
        state.pending = 0;
        state.lastFrame = null;
        state.velocity = 0;
        return 0;
    }
    const elapsed = state.lastFrame === null
        ? 1000 / 60
        : clampScroll(now - state.lastFrame, 0, 34);
    state.lastFrame = now;
    if (elapsed <= 0) return 0;
    if (Math.abs(state.pending) < 0.35) {
        const step = state.pending;
        state.pending = 0;
        state.velocity = 0;
        return step;
    }
    const step = state.pending * (1 - Math.exp(-elapsed / SCROLL_INERTIA_TAU_MS));
    state.pending -= step;
    state.velocity = step * 1000 / elapsed;
    return step;
}
