import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import {
    VALUES_FULL_FRONT, VALUES_HOLD_RETENTION, VALUES_OLD_EXIT,
    valuesSourceProgress, valuesCompactedProgress, valuesExitScrollOffset,
} from '../../resources/js/sections/values-card-keyframes.js';
import { desktopCardFrame } from '../../resources/js/sections/values-card-desktop.js';

const oldHold = VALUES_OLD_EXIT - VALUES_FULL_FRONT;
const originalScale = VALUES_FULL_FRONT
    + oldHold * VALUES_HOLD_RETENTION + (1 - VALUES_OLD_EXIT);
const frontEnd = VALUES_FULL_FRONT / originalScale;
const exitStart = (VALUES_FULL_FRONT + oldHold * VALUES_HOLD_RETENTION) / originalScale;

test('pre-exit card flip and hold retain their original pure choreography', () => {
    const geometry = {
        slots: [0, 1, 2, 3].map(i => ({ centerX: 100 + i * 300, centerY: 400 })),
        stageCenterX: 550, stageCenterY: 400,
        stageHeight: 800, cardWidth: 280, cardHeight: 400,
    };
    for (const source of [0, .006, .04, .052, .118, .2, .237,
        VALUES_FULL_FRONT, .4, .65, VALUES_OLD_EXIT]) {
        const remapped = valuesSourceProgress(valuesCompactedProgress(source));
        assert.ok(Math.abs(source - remapped) < 1e-10, `source=${source} mapped=${remapped}`);
        for (let card = 0; card < 4; card++) {
            const actual = desktopCardFrame(card, remapped, geometry, 0);
            const expected = desktopCardFrame(card, source, geometry, 0);
            for (const property of Object.keys(expected)) {
                assert.ok(Math.abs(actual[property] - expected[property]) < 1e-9,
                    `card=${card} source=${source} property=${property}: ${actual[property]} vs ${expected[property]}`);
            }
        }
    }
    assert.equal(valuesSourceProgress(0), 0);
    assert.equal(valuesSourceProgress(1), 1);
});

test('desktop fully visible hold retains 40% of old distance', () => {
    assert.ok(Math.abs(((exitStart - frontEnd) * originalScale) / oldHold
        - VALUES_HOLD_RETENTION) < 1e-12);
    for (const headerTopVh of [20, 35, 40, 55, 65]) {
        for (const viewportVh of [90, 92, 95]) {
            const originalTravel = 490 - headerTopVh - viewportVh;
            const reducedTravel = 365 - headerTopVh - viewportVh;
            const retained = (exitStart - frontEnd) * reducedTravel
                / (oldHold * originalTravel);
            assert.ok(retained >= .35 && retained <= .45,
                `header=${headerTopVh} viewport=${viewportVh} retained=${retained}`);
        }
    }
});

test('card exit travels exactly one pixel per physical scroll pixel, including reversal', () => {
    const start = valuesCompactedProgress(VALUES_OLD_EXIT);
    for (const span of [430, 1000, 1887, 2400]) {
        assert.equal(valuesExitScrollOffset(start, span), 0);
        assert.equal(valuesExitScrollOffset(start - .02, span), 0);
        const [a, b] = [start + .02, start + .05];
        const traveled = valuesExitScrollOffset(b, span) - valuesExitScrollOffset(a, span);
        assert.ok(Math.abs(traveled - (b - a) * span) < 1e-10);
        assert.equal(valuesExitScrollOffset(a, span), valuesExitScrollOffset(a, span));
        assert.ok(valuesExitScrollOffset(1, span) <= span);
        assert.ok(valuesExitScrollOffset(start, span) < valuesExitScrollOffset(b, span));
    }
});

test('only enhanced desktop runway and SVG field shrink in CSS', async () => {
    const [cards, line] = await Promise.all([
        fs.readFile(new URL('../../resources/css/sections/values-cards-layout.css', import.meta.url), 'utf8'),
        fs.readFile(new URL('../../resources/css/sections/values-line.css', import.meta.url), 'utf8'),
    ]);
    assert.match(cards, /@media \(min-width: 1280px\)/);
    assert.match(cards, /block-size: calc\(365svh - var\(--values-cards-start/);
    assert.match(line, /\.values\[data-line-ready\]:not\(\[data-cards-flow\]\) \{ min-block-size: 375svh; \}/);
    assert.match(line, /\.values\[data-line-ready\]:not\(\[data-cards-flow\]\) \.values__line \{ block-size: 375svh; \}/);
    assert.match(line, /\.values\[data-line-ready\] \{ min-block-size: 500vh; min-block-size: 500svh; \}/);
});
