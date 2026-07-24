import { clamp, ease, segment } from './geometry.js';
const PHASE = {
    enter: 0.9,
    settle: 0.42,
    frame: 1,
    hold: 0.72,
    connector: 1.08,
    transition: 0.92,
    finish: 0.48,
    outro: 0.9,
};
const RESISTANCE = 0.34;
const PIXELS_PER_STEP_VIEWPORT = 0.95;
const DAMPING = 0.085;
const SETTLED_EPSILON = 0.00035;
const BOUNDARY_EPSILON = 0.004;
const VISUAL_OUTRO_LIMIT = 0.72;
const createTimeline = (stepCount) => {
    const steps = [];
    let cursor = 0;
    let pendingEnter = null;
    for (let index = 0; index < stepCount; index += 1) {
        const step = { index };
        if (index === 0) {
            step.enterStart = -PHASE.enter;
            step.enterEnd = 0;
        } else {
            step.enterStart = pendingEnter.start;
            step.enterEnd = pendingEnter.end;
        }
        step.settleStart = cursor;
        cursor += PHASE.settle;
        step.settleEnd = cursor;
        step.frameStart = cursor;
        cursor += PHASE.frame;
        step.frameEnd = cursor;
        step.holdStart = cursor;
        cursor += PHASE.hold;
        step.holdEnd = cursor;
        if (index < stepCount - 1) {
            step.connectorStart = cursor;
            cursor += PHASE.connector;
            step.connectorEnd = cursor;
            step.transitionStart = cursor;
            cursor += PHASE.transition;
            step.transitionEnd = cursor;
            pendingEnter = { start: step.transitionStart, end: step.transitionEnd };
        } else {
            step.finishStart = cursor;
            cursor += PHASE.finish;
            step.finishEnd = cursor;
            step.outroStart = cursor;
            cursor += PHASE.outro;
            step.outroEnd = cursor;
        }
        steps.push(step);
    }
    return { steps, total: Math.max(cursor, 1), finalStep: steps.at(-1) };
};
const ensureTimeline = (state) => {
    if (!state.timeline) state.timeline = createTimeline(state.frames.length);
    return state.timeline;
};
const sampleTimeline = (state) => {
    const timeline = ensureTimeline(state);
    const unit = clamp(state.renderedProgress) * timeline.total;
    const lastIndex = timeline.steps.length - 1;
    const final = timeline.finalStep;
    const outro = final ? ease(segment(unit, final.outroStart, final.outroEnd)) : 0;
    const visualOutro = outro * VISUAL_OUTRO_LIMIT;
    const steps = timeline.steps.map((step) => {
        const entering = ease(segment(unit, step.enterStart, step.enterEnd));
        const exiting = step.index < lastIndex
            ? ease(segment(unit, step.transitionStart, step.transitionEnd))
            : visualOutro;
        return {
            entering,
            exiting,
            visible: clamp(entering * (1 - exiting)),
            frame: ease(segment(unit, step.frameStart, step.frameEnd)),
            connector: step.index < lastIndex
                ? ease(segment(unit, step.connectorStart, step.connectorEnd))
                : 0,
            lit: unit >= step.frameEnd,
            holding: unit >= step.holdStart && unit < step.holdEnd,
        };
    });
    let activeIndex = 0;
    let activeVisibility = -1;
    steps.forEach((step, index) => {
        if (step.visible > activeVisibility) {
            activeVisibility = step.visible;
            activeIndex = index;
        }
    });
    const finish = final ? ease(segment(unit, final.finishStart, final.finishEnd)) : 0;
    const cta = finish * (1 - (outro * 0.78));
    return { steps, activeIndex, cta, outro };
};
export const advanceTarget = (state, pixelDelta) => {
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
    const inputRange = Math.max(
        viewportHeight * state.frames.length * PIXELS_PER_STEP_VIEWPORT,
        viewportHeight * 1.6,
    );
    state.targetProgress = clamp(state.targetProgress + ((pixelDelta * RESISTANCE) / inputRange));
};
export const isJourneyReleaseReady = (state) => {
    if (!state || state.targetProgress < 1 - BOUNDARY_EPSILON) return false;
    const timeline = ensureTimeline(state);
    const final = timeline.finalStep;
    if (!final) return state.renderedProgress >= 1 - BOUNDARY_EPSILON;
    return (clamp(state.renderedProgress) * timeline.total)
        >= final.outroEnd - SETTLED_EPSILON;
};
export const canConsumeDirection = (state, direction) => {
    if (!state || !direction) return false;
    if (direction < 0) {
        return state.targetProgress > BOUNDARY_EPSILON
            || state.renderedProgress > BOUNDARY_EPSILON;
    }
    return !isJourneyReleaseReady(state);
};
const dampProgress = (state, timestamp) => {
    const elapsed = state.lastRenderTime
        ? Math.min(Math.max(timestamp - state.lastRenderTime, 4), 40)
        : 16.67;
    state.lastRenderTime = timestamp;
    const alpha = 1 - Math.pow(1 - DAMPING, elapsed / 16.67);
    const delta = state.targetProgress - state.renderedProgress;
    state.renderedProgress += delta * alpha;
    if (Math.abs(delta) >= SETTLED_EPSILON) return true;
    state.renderedProgress = state.targetProgress;
    return false;
};
const renderCard = (frame, sample) => {
    const side = frame.index % 2 === 0 ? 1 : -1;
    const x = ((1 - sample.entering) * side * 48) - (sample.exiting * side * 46);
    const y = ((1 - sample.entering) * 18) - (sample.exiting * 42);
    const scale = 0.965 + (sample.visible * 0.035) - (sample.exiting * 0.025);
    const blur = ((1 - sample.visible) * 5.5) + (sample.exiting * 4.5);
    frame.element.style.opacity = sample.visible.toFixed(4);
    frame.element.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) scale(${scale.toFixed(4)})`;
    frame.element.style.filter = `blur(${blur.toFixed(2)}px)`;
    frame.element.style.pointerEvents = sample.visible > 0.72 ? 'auto' : 'none';
    frame.path.style.strokeDashoffset = String(frame.length * (1 - sample.frame));
    frame.path.style.opacity = sample.frame > 0.002 ? '1' : '0';
    frame.node.classList.toggle('is-story-lit', sample.lit);
    frame.node.classList.toggle('is-story-holding', sample.holding);
    frame.path.classList.toggle('is-story-lit', sample.lit);
    frame.path.classList.toggle('is-story-holding', sample.holding);
};
const updateAccessibility = (state, activeIndex, status) => {
    state.frames.forEach((frame, index) => {
        frame.element.inert = index !== activeIndex;
    });
    if (state.activeIndex === activeIndex) return;
    state.activeIndex = activeIndex;
    const active = state.frames[activeIndex];
    if (!active || !status) return;
    const number = active.node.querySelector('.ppdb-liftoff-step')?.textContent?.trim() || '';
    const title = active.node.querySelector('h3')?.textContent?.trim() || '';
    status.textContent = [number, title].filter(Boolean).join(' — ');
};
const renderStageOutro = (state, outro) => {
    const root = state.panel.closest('[data-ppdb-liftoff]');
    if (!root) return;
    root.style.setProperty('--ppdb-journey-top-opacity', (1 - (outro * 0.82)).toFixed(4));
    root.style.setProperty('--ppdb-journey-top-y', `${(-28 * outro).toFixed(2)}px`);
    root.style.setProperty('--ppdb-journey-top-blur', `${(4 * outro).toFixed(2)}px`);
    root.classList.toggle('is-journey-outro', outro > 0.002);
};
export const renderProgress = (state, status, cta) => {
    if (!state) return;
    const sample = sampleTimeline(state);
    state.frames.forEach((frame, index) => renderCard(frame, sample.steps[index]));
    state.connectors.forEach((connector, index) => {
        const progress = sample.steps[index].connector;
        connector.path.style.strokeDashoffset = String(connector.length * (1 - progress));
        connector.path.style.opacity = progress > 0.002 ? '1' : '0';
    });
    if (cta) {
        cta.style.opacity = sample.cta.toFixed(4);
        cta.style.transform = `translateY(${((1 - sample.cta) * 16).toFixed(2)}px)`;
        cta.style.pointerEvents = sample.cta > 0.88 ? 'auto' : 'none';
    }
    renderStageOutro(state, sample.outro);
    updateAccessibility(state, sample.activeIndex, status);
};
export const renderFrame = (state, timestamp, status, cta) => {
    const unsettled = dampProgress(state, timestamp);
    renderProgress(state, status, cta);
    return unsettled;
};
