import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';

const source = await fs.readFile(new URL('../../resources/views/landing/values-line.blade.php', import.meta.url), 'utf8');
const tags = [...source.matchAll(/<path[^>]*\sd="([^"]+)"[^>]*\/>/gs)];
const paths = tags.map(match => {
    const nums = match[1].match(/-?\d+(?:\.\d+)?/g).map(Number);
    const curves = [];
    let last = nums.slice(0, 2);
    for (let i = 2; i < nums.length; i += 6) {
        const next = [last, nums.slice(i, i + 2), nums.slice(i + 2, i + 4), nums.slice(i + 4, i + 6)];
        assert.ok(next.every(point => point.length === 2));
        curves.push(next); last = next[3];
    }
    const coords = [curves[0][0]];
    for (const c of curves) for (let i = 1; i <= 240; i++) {
        const t = i / 240, u = 1 - t;
        coords.push([0, 1].map(a => u ** 3 * c[0][a] + 3 * u ** 2 * t * c[1][a]
            + 3 * u * t ** 2 * c[2][a] + t ** 3 * c[3][a]));
    }
    return { curves, coords };
});
const width = 1920, height = 5400;
const intersections = (first, second) => {
    const left = Math.max(first.coords[0][1], second.coords[0][1]);
    const right = Math.min(first.coords.at(-1)[1], second.coords.at(-1)[1]);
    if (right <= left) return [];
    const xAtY = (points, y) => {
        let low = 0, high = points.length - 1;
        while (high - low > 1) {
            const mid = (low + high) >> 1;
            if (points[mid][1] < y) low = mid; else high = mid;
        }
        const a = points[low], b = points[high];
        return a[0] + (b[0] - a[0]) * (y - a[1]) / (b[1] - a[1]);
    };
    const found = [];
    let previous = xAtY(first.coords, left) - xAtY(second.coords, left);
    for (let i = 1; i <= 3600; i++) {
        const y = left + (right - left) * i / 3600;
        const next = xAtY(first.coords, y) - xAtY(second.coords, y);
        if (previous * next < 0) found.push(y);
        previous = next;
    }
    return found;
};
test('three floor-like strands sweep sideways with continuous tangents', () => {
    assert.equal(paths.length, 3);
    for (const { curves, coords } of paths) {
        assert.ok(curves.length >= 2 && curves.length <= 5, 'each line has several directional bends');
        assert.ok(coords.some(p => p[0] > width * .7) && coords.some(p => p[0] < width * .3), 'wide sideways sweep');
        for (let i = 1; i < curves.length; i++) {
            const before = curves[i - 1], after = curves[i];
            for (const axis of [0, 1]) assert.equal(before[3][axis] - before[2][axis],
                after[1][axis] - after[0][axis], 'tangent continues without a sharp reversal');
        }
        assert.ok(curves.flat().every(p => p[1] > 0 && p[1] <= height));
        assert.ok(coords.at(-1)[1] > coords[0][1], 'strand spans its scroll interval');
    }
});
test('exactly one pair of paths crosses once; other pairs never cross', () => {
    assert.equal(intersections(paths[0], paths[1]).length, 1);
    assert.equal(intersections(paths[0], paths[2]).length, 0);
    assert.equal(intersections(paths[1], paths[2]).length, 0);
    const y = intersections(paths[0], paths[1])[0];
    assert.ok(y > 1700 && y < 2700);
});

test('soft S bends never cross their own strand', () => {
    const cross = (a, b, c) => (b[0] - a[0]) * (c[1] - a[1]) - (b[1] - a[1]) * (c[0] - a[0]);
    for (const { coords } of paths) for (let i = 0; i < coords.length - 1; i++) {
        for (let j = i + 2; j < coords.length - 1; j++) {
            const [a, b, c, d] = [coords[i], coords[i + 1], coords[j], coords[j + 1]];
            assert.ok(!(cross(a, b, c) * cross(a, b, d) < 0 && cross(c, d, a) * cross(c, d, b) < 0),
                'no self-crossing adds an unintended intersection');
        }
    }
});
