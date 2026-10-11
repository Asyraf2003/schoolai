import test from 'node:test';
import assert from 'node:assert/strict';
import {
    createPacingState, queuePacingInput, takePacingStep, wheelPixels,
    SCROLL_SPEED_PX_PER_SECOND, SCROLL_QUEUE_LIMIT_PX,
} from '../../resources/js/sections/scroll-pacing-core.js';

test('normalizes pixel/line/page wheel without modifying horizontal input', () => {
    assert.equal(wheelPixels({ deltaY: 35, deltaMode: 0 }, 900), 35);
    assert.equal(wheelPixels({ deltaY: -2, deltaMode: 1 }, 900), -36);
    assert.equal(wheelPixels({ deltaY: 1, deltaMode: 2 }, 900), 900);
    assert.equal(wheelPixels({ deltaY: Infinity, deltaMode: 0 }, 900), 0);
});

test('extreme wheel bursts stay capped and do not accumulate scroll debt', () => {
    const state = createPacingState();
    queuePacingInput(state, 30000, 100);
    assert.equal(state.pending, SCROLL_QUEUE_LIMIT_PX);
    let total = 0;
    for (const time of [116.67, 133.34, 150.01]) {
        const step = takePacingStep(state, time);
        assert.ok(step <= SCROLL_SPEED_PX_PER_SECOND * .017 + .1);
        total += step;
    }
    assert.ok(total < 45);
    assert.ok(state.pending <= SCROLL_QUEUE_LIMIT_PX);
    // A stop must not turn thousands of discarded wheel pixels into inertia.
    assert.equal(takePacingStep(state, 300), 0);
    assert.equal(state.pending, 0);
});

test('reverse direction cancels pending travel instantly', () => {
    const state = createPacingState();
    queuePacingInput(state, 5000, 100);
    takePacingStep(state, 116.67);
    queuePacingInput(state, -22, 120);
    assert.equal(state.pending, -22);
    const step = takePacingStep(state, 133.34);
    assert.ok(step < 0);
});

test('sustained high-rate events cannot exceed real-time scroll velocity', () => {
    const state = createPacingState();
    let travel = 0;
    for (let i = 0; i < 120; i++) {
        const time = 100 + (i + 1) * 1000 / 60;
        queuePacingInput(state, 4000, time - 1);
        travel += takePacingStep(state, time);
    }
    assert.ok(travel < SCROLL_SPEED_PX_PER_SECOND * 2.03);
    assert.ok(travel > SCROLL_SPEED_PX_PER_SECOND * 1.9);
});
