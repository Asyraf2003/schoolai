import { valuesFlipTravel, valuesPaintTravel, compactFlipTravel } from '../../resources/js/sections/scroll-pacing-poses.js';
import { flipAngle, flipLocal, responsiveFlipAngle, valuesCompactedProgress, valuesSourceProgress } from '../../resources/js/sections/values-card-keyframes.js';
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

test('free scroll is approximately 2.5 times the prior sustained 1680 baseline', () => {
    const state = createPacingState();
    let travel = 0;
    for (let index = 0; index < 120; index++) {
        const time = 100 + index * 1000 / 60;
        queuePacingInput(state, 112, time);
        travel += takePacingStep(state, time);
        assert.ok(state.pending <= SCROLL_DEBT_REFERENCE_PX);
    }
    assert.ok(travel > 8000 && travel < 8600, `2s free travel ${travel}`);
});

test('release glides with diminishing steps and stops without a long queue', () => {
    const state = createPacingState();
    queuePacingInput(state, 112, 100);
    const steps = Array.from({ length: 16 }, (_, index) => takePacingStep(state, 100 + index * 1000 / 60));
    assert.ok(steps.every(step => step > 0));
    assert.ok(steps.every((step, index) => !index || step < steps[index - 1]));
    const travel = steps.reduce((sum, step) => sum + step, 0);
    assert.ok(travel > 400 && travel < 550, `Glide distance ${travel}`);
    assert.equal(takePacingStep(state, 1700), 0);
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
    assert.ok(Math.abs(limitPacingTravel(100, 200, ranges, 200) - 240 / 8) < 1e-9,
        'phase geometry bounds travel independently of its CSS clock');
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


test('adaptive Values budget preserves the original flip angles and stagger both ways', () => {
    const span = 1900;
    for (const direction of [1, -1]) {
        let progress = valuesCompactedProgress(direction > 0 ? .052 : .308);
        let mixed = false, samples = 0;
        while (direction > 0 ? progress < valuesCompactedProgress(.308) : progress > valuesCompactedProgress(.052)) {
            const next = progress + direction * valuesFlipTravel(progress, span, direction) / span;
            const angles = [0, 1, 2, 3].map(index => flipAngle(flipLocal(index, valuesSourceProgress(next))));
            angles.forEach((angle, index) => assert.ok(Math.abs(angle - flipAngle(flipLocal(index, valuesSourceProgress(progress)))) <= 45.01));
            mixed ||= angles.some(angle => angle > 90) && angles.some(angle => angle < 90);
            progress = next;
            assert.ok(++samples < 20, 'no unnecessary long hold through gentle poses');
        }
        assert.ok(mixed, 'staggered face crossing is sampled in each direction');
    }
});


test('several deliveries before one paint share one Values angular budget', () => {
    const painted = valuesCompactedProgress(.15);
    const first = valuesPaintTravel(painted, painted, 1900, 1);
    assert.ok(first > 0);
    assert.ok(valuesPaintTravel(painted + first / 1900, painted, 1900, 1) < .001);
    assert.ok(valuesPaintTravel(painted + first / 1900, painted, 1900, -1) > 0);
    for (const direction of [1, -1]) {
        const native = .5, angle = responsiveFlipAngle(native);
        const step = compactFlipTravel(native, angle, 200, direction);
        assert.ok(Math.abs(responsiveFlipAngle(native + direction * step / 200) - angle) < 45.01);
        assert.ok(compactFlipTravel(native + direction * step / 200, angle, 200, direction) < .001);
    }
});


test('a spent pose budget blocks another delivery but never traps a phase endpoint', () => {
    const range = [{ start: 100, end: 200, speed: Infinity, travel: () => 0 }];
    assert.equal(limitPacingTravel(40, 16.67, range, 150), 0);
    assert.equal(limitPacingTravel(40, 16.67, range, 200), 40);
    assert.equal(limitPacingTravel(-40, 16.67, range, 100), -40);
});
