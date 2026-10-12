import test from 'node:test';
import assert from 'node:assert/strict';
import { testimonialDepartureStart, testimonialIntroFrame, testimonialWordFrame, testimonialTrackOffset } from '../../resources/js/sections/testimonials-motion.js';

test('Gallery-like white words read on black before rising/blur with white field and wall', () => {
    assert.equal(testimonialIntroFrame(0).tone, 8);
    assert.equal(testimonialWordFrame(0, 0, 7).opacity, 0);
    for (let index = 0; index <= 100; index++) {
        const frame = testimonialIntroFrame(index / 100);
        if (testimonialWordFrame(index / 100, 0, 7).opacity === 1) {
            const channel = frame.tone / 255;
            const luminance = channel <= .04045 ? channel / 12.92 : ((channel + .055) / 1.055) ** 2.4;
            assert.ok(1.05 / (luminance + .05) >= 7, `contrast at ${index}%`);
        }
    }
    assert.ok(testimonialWordFrame(.05, 0, 7).opacity > testimonialWordFrame(.05, 6, 7).opacity);
    assert.ok(testimonialWordFrame(.3, 0, 7).blur < testimonialWordFrame(.3, 6, 7).blur);
    assert.deepEqual(testimonialWordFrame(.5, 6, 7), { opacity: 1, blur: 0 });
    const departure = testimonialIntroFrame(.96);
    assert.ok(departure.y < 0 && departure.rows === 1);
    assert.ok(testimonialWordFrame(.96, 0, 7).blur > 0);
    assert.equal(testimonialIntroFrame(1).tone, 255);
    assert.equal(testimonialIntroFrame(1).rows, 1);
    assert.ok(Math.abs(testimonialIntroFrame(1).y + .16) < 1e-10);
    assert.deepEqual(testimonialWordFrame(1, 6, 7), { opacity: 0, blur: 14 });
    assert.deepEqual(testimonialIntroFrame(-1), testimonialIntroFrame(0));
    assert.deepEqual(testimonialIntroFrame(2), testimonialIntroFrame(1));
});

test('sharp reading hold equals the entire reveal distance before departure', () => {
    for (const duration of [.08, .12, .20]) {
        const reveal = .22 + duration;
        const depart = testimonialDepartureStart(duration);
        assert.ok(Math.abs(depart - reveal - reveal) < 1e-10);
        for (const p of [reveal + .001, reveal * 1.5, depart]) {
            assert.deepEqual(testimonialWordFrame(p, 6, 7, duration), { opacity: 1, blur: 0 });
            assert.deepEqual(testimonialIntroFrame(p, duration), { tone: 8, y: -0, rows: 0 });
        }
        assert.ok(testimonialIntroFrame(depart + .001, duration).y < 0);
        assert.ok(testimonialWordFrame(depart + .001, 6, 7, duration).opacity < 1);
    }
});

test('OLD tracks alternate direction, mirror RTL, and reverse by native progress', () => {
    assert.equal(testimonialTrackOffset(.25, 1000, 0, false), -250);
    assert.equal(testimonialTrackOffset(.25, 1000, 1, false), -750);
    assert.equal(testimonialTrackOffset(.25, 1000, 2, true), 250);
    assert.equal(testimonialTrackOffset(.75, 1000, 1, true), 250);
});
