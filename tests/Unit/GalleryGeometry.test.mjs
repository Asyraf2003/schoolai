import test from 'node:test';
import assert from 'node:assert/strict';
import { galleryGeometry } from '../../resources/js/sections/gallery-geometry.js';

const near = (actual, expected) => assert.ok(Math.abs(actual - expected) < .01, `${actual} vs ${expected}`);

test('34-tile geometry keeps lead centered, aligns 11/12/11 bottoms, and avoids overlap at every tier', () => {
    for (const width of [360, 390, 639, 640, 767, 768, 1023, 1024, 1279, 1280, 1440, 1535, 1536, 1920, 2560]) {
        for (const height of [500, 900]) for (const rtl of [false, true]) {
            const items = galleryGeometry(width, height, rtl, 34);
            const thirty = galleryGeometry(width, height, rtl, 30);
            const original = galleryGeometry(width, height, rtl);
            assert.deepEqual(thirty.slice(0, 10), original, 'repeats extend the first pass');
            assert.deepEqual(items.slice(0, 30), thirty, 'bottom additions extend the 30-tile composition');
            assert.deepEqual(['left', 'center', 'right'].map(c => items.filter(i => i.column === c).length), [11, 12, 11]);
            assert.ok(items.every(i => i.x >= -.01 && i.x + i.width <= width + .01 && i.scale > 0));
            near(items[0].y + items[0].height / 2, height / 2);
            near(items[0].x + items[0].width / 2, width / 2);
            assert.ok(items[0].openingScale > items[0].scale, 'the lead still shrinks in short landscape');
            const gap = Math.max(6, Math.min(width * .011, 16));
            const track = Math.min(width, height * 3.2);
            near(items[0].width, (track - 2 * gap) * .4);
            near(items[1].width, (track - 2 * gap) * .3);
            near(items[7].width, items[1].width);
            near(items[4].y, items[0].y + items[0].height + gap);

            const colBottom = col => Math.max(...items.filter(i => i.column === col).map(i => i.y + i.height));
            assert.ok(Math.abs(colBottom('center') - colBottom('left')) < items[1].height, 'left bottom aligns near center');
            assert.ok(Math.abs(colBottom('center') - colBottom('right')) < items[7].height, 'right bottom aligns near center');

            for (let a = 0; a < items.length; a++) for (let b = a + 1; b < items.length; b++) {
                const x = items[a], y = items[b];
                assert.ok(x.x + x.width <= y.x + .01 || y.x + y.width <= x.x + .01
                    || x.y + x.height <= y.y + .01 || y.y + y.height <= x.y + .01);
            }
            const tail = Math.max(...items.map(item => item.y + item.height));
            const rise = Math.max(0, tail - height);
            for (const item of items) {
                const entering = Math.min(rise, Math.max(0, item.y - height / 2));
                assert.ok(item.y - entering < height && item.y + item.height - entering > 0, 'every tile comes into view');
            }
        }
    }
});
