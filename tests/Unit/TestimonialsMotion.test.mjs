import test from 'node:test';
import assert from 'node:assert/strict';
import { testimonialIntroFrame, testimonialTrackOffset } from '../../resources/js/sections/testimonials-motion.js';

test('black title starts only after readable light and ends on white', () => {
    assert.deepEqual(testimonialIntroFrame(0), { tone: 8, opacity: 0, y: 32 });
    for (let index = 0; index <= 100; index++) {
        const frame = testimonialIntroFrame(index / 100);
        if (frame.opacity > 0) {
            const channel = frame.tone / 255;
            const luminance = channel <= .04045 ? channel / 12.92 : ((channel + .055) / 1.055) ** 2.4;
            assert.ok((luminance + .05) / .05 >= 7, `contrast at ${index}%`);
        }
    }
    assert.deepEqual(testimonialIntroFrame(1), { tone: 255, opacity: 1, y: 0 });
    assert.deepEqual(testimonialIntroFrame(-1), testimonialIntroFrame(0));
    assert.deepEqual(testimonialIntroFrame(2), testimonialIntroFrame(1));
});

test('OLD tracks alternate direction, mirror RTL, and reverse by native progress', () => {
    assert.equal(testimonialTrackOffset(.25, 1000, 0, false), -250);
    assert.equal(testimonialTrackOffset(.25, 1000, 1, false), -750);
    assert.equal(testimonialTrackOffset(.25, 1000, 2, true), 250);
    assert.equal(testimonialTrackOffset(.75, 1000, 1, true), 250);
});
