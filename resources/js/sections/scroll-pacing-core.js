export const SCROLL_SPEED_PX_PER_SECOND = 840;
export const SCROLL_QUEUE_LIMIT_PX = 112;
export const SCROLL_IDLE_CUTOFF_MS = 145;

export const clampScroll = (value, lower, upper) => Math.max(lower, Math.min(upper, value));

export function wheelPixels(event, viewportHeight) {
    if (!Number.isFinite(event.deltaY)) return 0;
    const unit = event.deltaMode === 1 ? 18 : event.deltaMode === 2 ? viewportHeight : 1;
    return event.deltaY * unit;
}

export function createPacingState() {
    return { pending: 0, lastInput: 0, lastFrame: null };
}

export function queuePacingInput(state, delta, now) {
    if (!Number.isFinite(delta) || !delta) return;
    if (state.pending * delta < 0) state.pending = 0;
    state.pending = clampScroll(state.pending + delta,
        -SCROLL_QUEUE_LIMIT_PX, SCROLL_QUEUE_LIMIT_PX);
    state.lastInput = now;
}

export function takePacingStep(state, now) {
    if (!state.pending || now - state.lastInput > SCROLL_IDLE_CUTOFF_MS) {
        state.pending = 0;
        state.lastFrame = null;
        return 0;
    }
    const elapsed = state.lastFrame === null ? 1000 / 60
        : clampScroll(now - state.lastFrame, 0, 34);
    state.lastFrame = now;
    const speedBudget = SCROLL_SPEED_PX_PER_SECOND * elapsed / 1000;
    const step = Math.sign(state.pending) * Math.min(Math.abs(state.pending), speedBudget);
    state.pending -= step;
    if (Math.abs(state.pending) < .01) state.pending = 0;
    return step;
}
