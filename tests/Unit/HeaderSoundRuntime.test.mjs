import test from 'node:test';
import assert from 'node:assert/strict';
import { advanceAmplitude, wavePoints } from '../../resources/js/sections/header-sound.js';

test('Sound grows gradually, reverses from current amplitude and settles at either endpoint', () => {
    const rising = advanceAmplitude(0, true, 0.25);
    assert.equal(rising, 0.25);
    assert.equal(advanceAmplitude(rising, false, 0.1), 0.15);
    assert.equal(advanceAmplitude(rising, false, 1), 0);
    assert.equal(advanceAmplitude(rising, true, 1), 1);
});

test('Muted sound is one straight line at any phase and active wave stays inside its control', () => {
    for (const size of [44, 46, 64]) {
        for (const phase of [0, 0.25, 1, 10]) {
            const muted = wavePoints(size, 0, phase);
            assert.ok(muted.every(([, y]) => y === size / 2));
            assert.equal(muted[0][0], size * 0.3);
            assert.equal(muted.at(-1)[0], size * 0.7);
            const active = wavePoints(size, 1, phase);
            assert.ok(active.some(([, y]) => y !== size / 2));
            assert.ok(active.every(([x, y]) => x >= 0 && x <= size && y > 0 && y < size));
        }
    }
});
