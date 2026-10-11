import test from 'node:test';
import assert from 'node:assert/strict';
import { galleryGeometry } from '../../resources/js/sections/gallery-geometry.js';

test('ten media form balanced 3–4–3 columns, mirrored without overlap at every tier', () => {
    for (const width of [360, 390, 639, 640, 767, 768, 1023, 1024, 1279, 1280, 1440, 1535, 1536, 1920]) {
        for (const height of [500, 900]) for (const rtl of [false, true]) {
            const items = galleryGeometry(width, height, rtl);
            assert.equal(items.length, 10);
            assert.deepEqual(['left', 'center', 'right'].map(c => items.filter(i => i.column === c).length), [3, 4, 3]);
            assert.ok(items.every(i => i.x >= 0 && i.x + i.width <= width && i.scale > 0));
            for (let a = 0; a < 10; a++) for (let b = a + 1; b < 10; b++) {
                const x = items[a], y = items[b];
                assert.ok(x.x + x.width <= y.x + .01 || y.x + y.width <= x.x + .01
                    || x.y + x.height <= y.y + .01 || y.y + y.height <= x.y + .01);
            }
            if (width >= 768) {
                const heights = ['left', 'center', 'right'].map(c => {
                    const column = items.filter(i => i.column === c);
                    return column.at(-1).y + column.at(-1).height - column[0].y;
                });
                assert.ok(Math.max(...heights) - Math.min(...heights) < .01);
                assert.ok(Math.abs(items[0].x + items[0].width / 2 - width / 2) < .01);
            }
        }
    }
});
