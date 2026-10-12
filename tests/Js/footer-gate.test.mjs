import test from 'node:test';
import assert from 'node:assert/strict';
import { createFooterGate } from '../../resources/js/sections/footer-gate.js';

const bounds = () => ({ white: 5000, blue: 5300 });

test('a large gesture stops completely at the white footer', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.wheel(1000, 4500, 10);
    assert.equal(gate.limit(1800, 4500), 500);
    assert.equal(gate.advanced(5000), true);
    assert.equal(gate.phase, 'white-held');
    assert.equal(gate.wheel(200, 5000, 30), true);
    assert.equal(gate.limit(1000, 5000), 0);
    assert.equal(redirects, 0);
});

test('white → blue needs a fresh gesture; blue → gallery needs two more', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.wheel(300, 4900, 10);
    gate.advanced(5000);
    assert.equal(gate.wheel(200, 5000, 150), true);
    assert.equal(gate.wheel(140, 5000, 500), false);
    assert.equal(gate.phase, 'to-blue');
    assert.equal(gate.limit(1200, 5000), 300);
    assert.equal(gate.advanced(5300), true);
    assert.equal(gate.phase, 'blue-held');
    assert.equal(gate.wheel(120, 5300, 530), true);
    assert.equal(gate.progress, 0, 'the entering gesture must not charge blue');
    assert.equal(gate.wheel(120, 5300, 850), true);
    assert.ok(gate.progress >= .5 && gate.progress < 1);
    assert.equal(redirects, 0);
    assert.equal(gate.wheel(120, 5300, 1150), true);
    assert.equal(gate.progress, 1);
    assert.equal(redirects, 1);
});

test('one extremely large gesture remains capped below completion', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.reset(5300);
    gate.wheel(9000, 5300, 10);
    const first = gate.progress;
    assert.ok(first >= .5 && first <= .55);
    gate.wheel(9000, 5300, 30);
    assert.ok(gate.progress <= .55);
    assert.equal(redirects, 0);
});

test('no scroll lets yellow progress drain gradually to zero', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.reset(5300);
    gate.wheel(140, 5300, 10);
    const first = gate.progress;
    gate.settle(400);
    assert.equal(gate.progress, first);
    gate.settle(1510);
    assert.ok(gate.progress < first && gate.progress > 0);
    gate.settle(4000);
    assert.equal(gate.progress, 0);
    assert.equal(redirects, 0);
});

test('a later gesture can refill the partially decayed bar', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.reset(5300);
    gate.wheel(140, 5300, 10);
    gate.settle(900);
    const partial = gate.progress;
    assert.ok(partial > 0 && partial < .5);
    gate.wheel(140, 5300, 920);
    assert.ok(gate.progress > partial);
    assert.equal(redirects, 0);
});

test('scrolling up resets progress and lets the reader return to Stories', () => {
    const gate = createFooterGate(bounds, () => {});
    gate.reset(5300);
    gate.wheel(140, 5300, 10);
    assert.ok(gate.progress > 0);
    assert.equal(gate.wheel(-140, 5300, 100), false);
    assert.equal(gate.progress, 0);
    gate.advanced(5100);
    assert.equal(gate.phase, 'to-blue');
    gate.advanced(4900);
    assert.equal(gate.phase, 'to-white');
    assert.equal(gate.limit(1200, 4900), 100);
});

test('small wheel tail cannot unlock the first or second checkpoint', () => {
    const gate = createFooterGate(bounds, () => {});
    gate.reset(5000);
    assert.equal(gate.wheel(8, 5000, 500), true);
    assert.equal(gate.phase, 'white-held');
    assert.equal(gate.wheel(150, 5000, 800), false);
    gate.advanced(5300);
    assert.equal(gate.wheel(8, 5300, 900), true);
    assert.equal(gate.progress, 0);
});
