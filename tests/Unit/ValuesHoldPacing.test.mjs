import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import {
    VALUES_FULL_FRONT, VALUES_HOLD_RETENTION, VALUES_OLD_EXIT,
    valuesSourceProgress, valuesCompactedProgress,
} from '../../resources/js/sections/values-card-keyframes.js';
import { desktopCardFrame } from '../../resources/js/sections/values-card-desktop.js';

const oldHold = VALUES_OLD_EXIT - VALUES_FULL_FRONT;
const originalScale = VALUES_FULL_FRONT
    + oldHold * VALUES_HOLD_RETENTION + (1 - VALUES_OLD_EXIT);
const frontEnd = VALUES_FULL_FRONT / originalScale;
const exitStart = (VALUES_FULL_FRONT + oldHold * VALUES_HOLD_RETENTION) / originalScale;

test('full card flip and exit retain their original pure choreography', () => {
    const geometry = {
        slots: [0, 1, 2, 3].map(i => ({ centerX: 100 + i * 300, centerY: 400 })),
        stageCenterX: 550, stageCenterY: 400,
        stageHeight: 800, cardWidth: 280, cardHeight: 400,
    };
    for (const source of [0, .006, .04, .052, .118, .2, .237,
        VALUES_FULL_FRONT, .4, .65, VALUES_OLD_EXIT, .92, .98, 1]) {
        const remapped = valuesSourceProgress(valuesCompactedProgress(source));
        assert.ok(Math.abs(source - remapped) < 1e-10, `source=${source} mapped=${remapped}`);
        for (let card = 0; card < 4; card++) {
            assert.deepEqual(desktopCardFrame(card, remapped, geometry, 0),
                desktopCardFrame(card, source, geometry, 0));
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
