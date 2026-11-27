import test from 'node:test';
import assert from 'node:assert/strict';
import { createPacingState, queuePacingInput, takePacingStep } from '../../resources/js/sections/scroll-pacing-core.js';

test('retired footer gate leaves scrolling unrestricted into and through footer', () => {
    const state = createPacingState();
    queuePacingInput(state, 500, 0);
    let total = 0;
    for (let t = 16; t <= 1800; t += 16) total += takePacingStep(state, t);
    assert.ok(Math.abs(total - 425) < 1, `unbounded 0.85x delivery expected ~425px, got ${total}`);
});
