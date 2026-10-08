import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { createLineProgress } from '../../resources/js/sections/values-line-progress.js';

test('one organic SVG curve has continuous tangent and curvature at every join', async () => {
    const source = await fs.readFile(new URL('../../resources/views/landing/values-line.blade.php', import.meta.url), 'utf8');
    assert.equal((source.match(/<path /g) ?? []).length, 1);
    const d = source.match(/ d="([^"]+)"/s)[1];
    const numbers = d.match(/-?\d+(?:\.\d+)?/g).map(Number);
    let start = numbers.splice(0, 2);
    let previous;
    for (let i = 0; i < numbers.length; i += 6) {
        const [x1, y1, x2, y2, x, y] = numbers.slice(i, i + 6);
        const current = [start, [x1, y1], [x2, y2], [x, y]];
        if (previous) for (const axis of [0, 1]) {
            const before = previous[3][axis] - previous[2][axis];
            const after = current[1][axis] - current[0][axis];
            assert.ok(Math.abs(before - after) < .003, 'tangent does not jump');
            const beforeCurve = previous[3][axis] - 2 * previous[2][axis] + previous[1][axis];
            const afterCurve = current[2][axis] - 2 * current[1][axis] + current[0][axis];
            assert.ok(Math.abs(beforeCurve - afterCurve) < .005, 'curvature does not jump');
        }
        previous = current; start = current[3];
    }
});

test('scroll mapping is monotonic, exact at seams and has continuous speed', () => {
    const stops = [0, 2000, 6500, 9900, 13500, 16000];
    const progress = createLineProgress(stops);
    let previous = 0;
    for (let index = 0; index <= 1000; index++) {
        const value = progress(index / 1000);
        assert.ok(value >= previous && value <= 1);
        previous = value;
    }
    for (let index = 1; index < 5; index++) {
        const p = index / 5;
        assert.ok(Math.abs(progress(p) - stops[index] / stops.at(-1)) < 1e-12);
        const left = (progress(p) - progress(p - 1e-6)) / 1e-6;
        const right = (progress(p + 1e-6) - progress(p)) / 1e-6;
        assert.ok(Math.abs(left - right) < .0001);
    }
    assert.equal(progress(0), 0); assert.equal(progress(1), 1);
});
