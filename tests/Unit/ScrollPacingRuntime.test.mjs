import test from 'node:test';
import assert from 'node:assert/strict';
import {
    createPacingState, queuePacingInput, takePacingStep, wheelPixels,
    SCROLL_INPUT_GAIN, SCROLL_INERTIA_TAU_MS, SCROLL_MAX_PENDING_PX,
} from '../../resources/js/sections/scroll-pacing-core.js';

test('normalizes pixel, line and page wheel input', () => {
    assert.equal(wheelPixels({ deltaY: 35, deltaMode: 0 }, 900), 35);
    assert.equal(wheelPixels({ deltaY: -2, deltaMode: 1 }, 900), -36);
    assert.equal(wheelPixels({ deltaY: 1, deltaMode: 2 }, 900), 900);
    assert.equal(wheelPixels({ deltaY: Infinity, deltaMode: 0 }, 900), 0);
});

test('uses a uniform 0.85x gain across the entire document without section speed changes', () => {
    const state = createPacingState();
    queuePacingInput(state, 112, 100);
    assert.ok(Math.abs(state.pending - 112 * SCROLL_INPUT_GAIN) < 1e-9);
    let travel = 0;
    for (let index = 0; index < 80; index++) {
        travel += takePacingStep(state, 100 + index * 1000 / 60);
    }
    assert.ok(Math.abs(travel - 112 * SCROLL_INPUT_GAIN) < 0.01, `total travel ${travel}`);
    assert.equal(state.pending, 0);
});

test('release glides with standard exponential inertia (20 -> 18 -> ... -> 0) and settles cleanly', () => {
    const state = createPacingState();
    assert.equal(SCROLL_INERTIA_TAU_MS, 180);
    queuePacingInput(state, 260, 100);
    const steps = Array.from({ length: 24 }, (_, index) => takePacingStep(state, 100 + index * 1000 / 60));
    assert.ok(steps[0] > 18 && steps[0] < 21, `initial inertia step ${steps[0]}`);
    assert.ok(steps.every(step => step > 0));
    assert.ok(steps.every((step, index) => !index || step < steps[index - 1]));
    assert.equal(takePacingStep(state, 2000), 0);
    assert.equal(state.pending, 0);
});

test('first synchronous delivery survives a delayed WebKit paint without replaying stale input', () => {
    const state = createPacingState();
    queuePacingInput(state, 5000, 100);
    assert.equal(state.pending, SCROLL_MAX_PENDING_PX);
    assert.ok(takePacingStep(state, 100) > 0);
    assert.equal(takePacingStep(state, 281), 0);
    assert.equal(state.pending, 0);
});

test('reversing input discards prior direction and responds immediately', () => {
    const state = createPacingState();
    queuePacingInput(state, 5000, 100);
    takePacingStep(state, 100);
    assert.equal(queuePacingInput(state, -40, 120), true);
    assert.ok(Math.abs(state.pending - -40 * SCROLL_INPUT_GAIN) < 1e-9);
    assert.ok(takePacingStep(state, 120) < 0);
});

test('sustained rolling maintains steady input rate and preserves exact total ratio', () => {
    const state = createPacingState();
    let travel = 0;
    for (let index = 0; index < 30; index++) {
        const time = 100 + index * 1000 / 60;
        queuePacingInput(state, 100, time);
        travel += takePacingStep(state, time);
    }
    for (let index = 30; index < 140; index++) {
        travel += takePacingStep(state, 100 + index * 1000 / 60);
    }
    assert.ok(Math.abs(travel - 30 * 100 * SCROLL_INPUT_GAIN) < 0.01, `sustained travel ${travel}`);
    assert.equal(state.pending, 0);
});

test('a frame with the same clock sample keeps accepted input available', () => {
    const state = createPacingState();
    queuePacingInput(state, 112, 100);
    takePacingStep(state, 100);
    const remaining = state.pending;
    assert.equal(takePacingStep(state, 100), 0);
    assert.equal(state.pending, remaining);
    assert.ok(takePacingStep(state, 116.67) > 0);
});
