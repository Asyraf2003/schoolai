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

test('white and blue stop separately before fresh input charges by distance', () => {
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
    assert.equal(gate.wheel(420, 5300, 850), true);
    assert.equal(gate.progress, .5);
    assert.equal(redirects, 0);
    assert.equal(gate.wheel(420, 5300, 950), true);
    assert.equal(gate.progress, 1);
    assert.equal(redirects, 1);
});

test('one sufficiently long gesture fills the bar and navigates only once', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.reset(5300);
    gate.wheel(9000, 5300, 10);
    assert.equal(gate.progress, 1);
    gate.wheel(9000, 5300, 30);
    assert.equal(gate.progress, 1);
    assert.equal(redirects, 1);
});

test('released progress takes twice the measured rise time to return to zero', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.reset(5300);
    gate.wheel(84, 5300, 0);
    gate.wheel(84, 5300, 100);
    gate.wheel(84, 5300, 200);
    const first = gate.progress;
    gate.settle(320);
    assert.equal(gate.progress, first);
    gate.settle(420);
    assert.ok(Math.abs(gate.progress - .225) < 1e-10);
    gate.settle(520);
    assert.ok(Math.abs(gate.progress - first / 2) < 1e-10);
    gate.settle(720);
    assert.ok(gate.progress < 1e-10);
    assert.equal(redirects, 0);
});

test('a later gesture can refill the partially decayed bar', () => {
    let redirects = 0;
    const gate = createFooterGate(bounds, () => redirects++);
    gate.reset(5300);
    gate.wheel(168, 5300, 0);
    gate.settle(220);
    const partial = gate.progress;
    assert.ok(partial > 0 && partial < .5);
    gate.wheel(84, 5300, 220);
    assert.ok(gate.progress > partial);
    assert.equal(redirects, 0);
});

test('input distance determines fill without a minimum jump or a gesture cap', () => {
    const small = createFooterGate(bounds, () => {});
    const large = createFooterGate(bounds, () => {});
    small.reset(5300); large.reset(5300);
    small.wheel(42, 5300, 0); large.wheel(84, 5300, 0);
    assert.equal(small.progress, .05);
    assert.equal(large.progress, small.progress * 2);
    small.wheel(2.1, 5300, 30);
    assert.ok(Math.abs(small.progress - .0525) < 1e-10, 'tiny finger motion still adds its actual distance');
    large.wheel(420, 5300, 30);
    assert.equal(large.progress, .6, 'same gesture can go beyond the old 55% ceiling');
});

test('splitting the same movement into more packets preserves total fill', () => {
    const single = createFooterGate(bounds, () => {});
    const packets = createFooterGate(bounds, () => {});
    single.reset(5300); packets.reset(5300);
    single.wheel(168, 5300, 0);
    packets.wheel(84, 5300, 0); packets.wheel(42, 5300, 30); packets.wheel(42, 5300, 60);
    assert.equal(single.progress, packets.progress);
});

test('even a tiny fresh movement starts a proportionally tiny fill at blue', () => {
    const gate = createFooterGate(bounds, () => {});
    gate.reset(5300);
    gate.wheel(2.1, 5300, 0);
    assert.equal(gate.progress, .0025);
});

test('decay follows elapsed time rather than frame count and reset clears its speed', () => {
    const sparse = createFooterGate(bounds, () => {});
    const frequent = createFooterGate(bounds, () => {});
    for (const gate of [sparse, frequent]) { gate.reset(5300); gate.wheel(168, 5300, 0); }
    sparse.settle(220);
    for (let now = 120; now <= 220; now += 10) frequent.settle(now);
    assert.ok(Math.abs(sparse.progress - frequent.progress) < 1e-10);
    frequent.reset(5300); frequent.settle(5000);
    assert.equal(frequent.progress, 0);
    assert.equal(frequent.wheel(Number.NaN, 5300, 5010), false);
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
