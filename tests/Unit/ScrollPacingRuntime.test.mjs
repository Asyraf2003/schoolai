import test from 'node:test';
import assert from 'node:assert/strict';
import {
    createPacingState, queuePacingInput, takePacingStep, wheelPixels,
    limitPacingTravel, SCROLL_DEBT_REFERENCE_PX,
} from '../../resources/js/sections/scroll-pacing-core.js';

test('normalizes pixel, line and page wheel input', () => {
    assert.equal(wheelPixels({ deltaY: 35, deltaMode: 0 }, 900), 35);
    assert.equal(wheelPixels({ deltaY: -2, deltaMode: 1 }, 900), -36);
    assert.equal(wheelPixels({ deltaY: 1, deltaMode: 2 }, 900), 900);
    assert.equal(wheelPixels({ deltaY: Infinity, deltaMode: 0 }, 900), 0);
});

test('free scroll is approximately twice the prior sustained 1680 baseline', () => {
    const state = createPacingState();
    let travel = 0;
    for (let index = 0; index < 120; index++) {
        const time = 100 + index * 1000 / 60;
        queuePacingInput(state, 112, time);
        travel += takePacingStep(state, time);
        assert.ok(state.pending <= SCROLL_DEBT_REFERENCE_PX);
    }
    assert.ok(travel > 6200 && travel < 6800, `2s free travel ${travel}`);
});

test('release glides with diminishing steps and stops without a long queue', () => {
    const state = createPacingState();
    queuePacingInput(state, 112, 100);
    const steps = Array.from({ length: 10 }, (_, index) => takePacingStep(state, 100 + index * 1000 / 60));
    assert.ok(steps.every(step => step > 0));
    assert.ok(steps.every((step, index) => !index || step < steps[index - 1]));
    assert.ok(steps.reduce((sum, step) => sum + step, 0) <= 224);
    assert.equal(takePacingStep(state, 300), 0);
    assert.equal(state.pending, 0);
});

test('first synchronous delivery survives a delayed WebKit paint without replaying stale input', () => {
    const state = createPacingState();
    queuePacingInput(state, 5000, 100);
    assert.ok(takePacingStep(state, 100) > 0);
    assert.equal(takePacingStep(state, 281), 0);
    assert.equal(state.pending, 0);
});

test('reversing input discards prior direction and responds immediately', () => {
    const state = createPacingState();
    queuePacingInput(state, 5000, 100);
    takePacingStep(state, 100);
    assert.equal(queuePacingInput(state, -22, 120), true);
    assert.equal(state.pending, -44);
    assert.ok(takePacingStep(state, 120) < 0);
});

test('phase velocity follows each physical range and boundaries cannot be skipped', () => {
    const ranges = [{ start: 100, end: 340, speed: 720 }, { start: 500, end: 1100, speed: 1800 }];
    assert.equal(limitPacingTravel(100, 1000 / 60, ranges, 200), 12);
    assert.ok(Math.abs(limitPacingTravel(100, 1000 / 60, ranges, 700) - 30) < 1e-9);
    assert.equal(limitPacingTravel(100, 1000 / 60, ranges, 50), 62);
    assert.equal(limitPacingTravel(-100, 1000 / 60, ranges, 390), -62);
    assert.equal(limitPacingTravel(100, 1000 / 60, ranges, 350), 100);
    assert.ok(Math.abs(limitPacingTravel(100, 200, ranges, 200) - 240 / 14) < 1e-9,
        'fourteen movement segments reserve both boundary paints and twelve intermediate states');
});

test('larger free input has no universal speed ceiling but debt still expires', () => {
    const normal = createPacingState();
    const aggressive = createPacingState();
    queuePacingInput(normal, 112, 100);
    queuePacingInput(aggressive, 10000, 100);
    assert.ok(takePacingStep(aggressive, 100) > takePacingStep(normal, 100) * 5);
    assert.equal(takePacingStep(aggressive, 281), 0);
    assert.equal(aggressive.pending, 0);
});

test('unused constrained input cannot accelerate itself on entering free space', () => {
    const state = createPacingState();
    queuePacingInput(state, 10000, 100);
    takePacingStep(state, 100, [{ start: 0, end: 140, speed: 600 }], 130);
    assert.ok(takePacingStep(state, 116.67, [], 141) <= 10.01);
    let coast = 0;
    for (let time = 150; time <= 300; time += 16.67) coast += takePacingStep(state, time);
    assert.ok(coast < 224);
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
