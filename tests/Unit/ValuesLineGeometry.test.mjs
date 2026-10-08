import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { createLineProgress } from '../../resources/js/sections/values-line-progress.js';

const source = await fs.readFile(new URL('../../resources/views/landing/values-line.blade.php', import.meta.url), 'utf8');
const numbers = source.match(/ d="([^"]+)"/s)[1].match(/-?\d+(?:\.\d+)?/g).map(Number);
const curves = [];
let start = numbers.slice(0, 2);
for (let i = 2; i < numbers.length; i += 6) {
    const curve = [start, numbers.slice(i, i + 2), numbers.slice(i + 2, i + 4), numbers.slice(i + 4, i + 6)];
    curves.push(curve); start = curve[3];
}

test('one organic SVG curve has continuous tangent and curvature at every join', async () => {
    assert.equal((source.match(/<path /g) ?? []).length, 1);
    let previous;
    for (const current of curves) {
        if (previous) for (const axis of [0, 1]) {
            const before = previous[3][axis] - previous[2][axis];
            const after = current[1][axis] - current[0][axis];
            assert.ok(Math.abs(before - after) < .003, 'tangent does not jump');
            const beforeCurve = previous[3][axis] - 2 * previous[2][axis] + previous[1][axis];
            const afterCurve = current[2][axis] - 2 * current[1][axis] + current[0][axis];
            assert.ok(Math.abs(beforeCurve - afterCurve) < .005, 'curvature does not jump');
        }
        previous = current;
    }
});

test('both line caps stay beyond a side wall and never use the top or bottom edge', () => {
    const [width, height] = source.match(/viewBox="0 0 (\d+) (\d+)"/).slice(1).map(Number);
    for (const point of [curves[0][0], curves.at(-1)[3]]) {
        assert.ok(point[0] < -12 || point[0] > width + 12, 'cap is clipped beyond a side wall');
    }
    assert.ok(curves.flat().every(point => point[1] > 0 && point[1] < height),
        'the entire Bezier convex hull stays away from top/bottom clipping');
});

test('the organic line has no crossing or touching non-adjacent segments', () => {
    const points = [curves[0][0]];
    for (const curve of curves) {
        // At 64 steps the second-derivative bound limits chord error to <0.1 SVG px.
        const acceleration = [0, 1].map(index => Math.hypot(...[0, 1].map(axis =>
            6 * (curve[index + 2][axis] - 2 * curve[index + 1][axis] + curve[index][axis]))));
        assert.ok(Math.max(...acceleration) / (8 * 64 ** 2) < .1);
        for (let step = 1; step <= 64; step++) {
            const t = step / 64; const u = 1 - t;
            points.push([0, 1].map(axis => u ** 3 * curve[0][axis] + 3 * u ** 2 * t * curve[1][axis]
                + 3 * u * t ** 2 * curve[2][axis] + t ** 3 * curve[3][axis]));
        }
    }
    const cross = (a, b, c) => (b[0] - a[0]) * (c[1] - a[1]) - (b[1] - a[1]) * (c[0] - a[0]);
    for (let i = 0; i < points.length - 1; i++) for (let j = i + 2; j < points.length - 1; j++) {
        const [a, b, c, d] = [points[i], points[i + 1], points[j], points[j + 1]];
        if ([0, 1].some(axis => Math.max(a[axis], b[axis]) < Math.min(c[axis], d[axis])
            || Math.max(c[axis], d[axis]) < Math.min(a[axis], b[axis]))) continue;
        assert.ok(cross(a, b, c) * cross(a, b, d) > 0 || cross(c, d, a) * cross(c, d, b) > 0,
            `line segments ${i}/${j} do not intersect`);
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
