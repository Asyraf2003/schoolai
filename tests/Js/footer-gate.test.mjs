import test from 'node:test';
import assert from 'node:assert/strict';
import { createFooterGate } from '../../resources/js/sections/footer-gate.js';

test('one large wheel input cannot pass the white full-screen stop', () => {
    let redirects = 0;
    const gate = createFooterGate(() => ({ white: 5000, blue: 5300 }), () => redirects++);
    assert.equal(gate.wheel(1000, 4500, 10), false);
    assert.equal(gate.limit(1800, 4500), 500);
    assert.equal(gate.advanced(5000), true);
    assert.equal(gate.phase, 'white-held');
    assert.equal(gate.wheel(200, 5000, 30), true);
    assert.equal(gate.limit(1000, 5000), 0);
    assert.equal(redirects, 0);
});

test('next separate wheel unlocks blue and following separate wheel navigates', () => {
    let redirects = 0;
    const gate = createFooterGate(() => ({ white: 5000, blue: 5300 }), () => redirects++);
    gate.wheel(300, 4900, 10);
    gate.advanced(5000);
    assert.equal(gate.wheel(200, 5000, 150), true);
    assert.equal(gate.wheel(140, 5000, 500), false);
    assert.equal(gate.phase, 'to-blue');
    assert.equal(gate.limit(1200, 5000), 300);
    assert.equal(gate.advanced(5300), true);
    assert.equal(gate.phase, 'blue-held');
    assert.equal(gate.wheel(120, 5300, 530), true);
    assert.equal(redirects, 0);
    assert.equal(gate.wheel(120, 5300, 850), true);
    assert.equal(redirects, 0);
    assert.ok(gate.progress > 0 && gate.progress < 1);
    for (let i = 0; i < 5; i++) gate.wheel(140, 5300, 880 + i * 30);
    assert.equal(redirects, 0);
    assert.ok(gate.progress > .9 && gate.progress < 1);
    assert.equal(gate.wheel(140, 5300, 1030), true);
    assert.equal(gate.progress, 1);
    assert.equal(redirects, 1);
});

test('small wheel tail cannot trigger stage unlock or redirect', () => {
    let redirects = 0;
    const gate = createFooterGate(() => ({ white: 5000, blue: 5300 }), () => redirects++);
    gate.reset(5000);
    assert.equal(gate.wheel(8, 5000, 500), true);
    assert.equal(gate.phase, 'white-held');
    assert.equal(gate.wheel(150, 5000, 800), false);
    gate.advanced(5300);
    assert.equal(gate.wheel(8, 5300, 1300), true);
    assert.equal(redirects, 0);
});

test('reverse input resets gate and permits moving back to stories', () => {
    const gate = createFooterGate(() => ({ white: 5000, blue: 5300 }), () => {});
    gate.reset(5300);
    assert.equal(gate.wheel(-140, 5300, 100), false);
    gate.advanced(5100);
    assert.equal(gate.phase, 'to-blue');
    gate.advanced(4900);
    assert.equal(gate.phase, 'to-white');
    assert.equal(gate.limit(1200, 4900), 100);
});

test('restoring blue requires progressive filling before navigation', () => {
    let count = 0;
    const gate = createFooterGate(() => ({ white: 5000, blue: 5300 }), () => count++);
    gate.reset(5300);
    assert.equal(gate.progress, 0);
    assert.equal(gate.wheel(140, 5300, 10), true);
    assert.equal(count, 0);
    assert.ok(gate.progress > 0 && gate.progress < 1);
    for (let i = 0; i < 5; i++) gate.wheel(140, 5300, 40 + i * 30);
    assert.equal(count, 1);
});

test('single extreme scroll packet cannot fill the progress line', () => {
    let redirected = 0;
    const gate = createFooterGate(() => ({ white: 5000, blue: 5300 }), () => redirected++);
    gate.reset(5300);
    gate.wheel(9000, 5300, 10);
    assert.ok(gate.progress < .2);
    assert.equal(redirected, 0);
});
