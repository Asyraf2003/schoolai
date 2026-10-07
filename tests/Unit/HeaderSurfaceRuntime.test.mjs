import test from 'node:test';
import assert from 'node:assert/strict';
import { createHeaderState, headerSurface } from '../../resources/js/sections/header-state.js';

test('Desktop panels own one light field until rendered closing finishes, then scroll owns tone', () => {
    const header = createHeaderState(() => {});
    header.send({ type: 'viewport', desktop: true });
    header.send({ type: 'panel', id: 'education', open: true });
    assert.equal(headerSurface(header.snapshot(), true), 'light');
    header.send({ type: 'dismiss' });
    assert.equal(headerSurface(header.snapshot(), true), 'light');
    assert.equal(headerSurface(header.snapshot(), false), 'hero');
    for (const [y, surface] of [[48, 'hero'], [49, 'light'], [30, 'light'], [24, 'hero']]) {
        header.send({ type: 'scroll', y, outsideHero: false });
        assert.equal(headerSurface(header.snapshot(), false), surface);
    }
    header.send({ type: 'dismiss' });
    assert.equal(headerSurface(header.snapshot(), false), 'hero');
});

test('Compact navigation retains its light field until the rendered panel has closed', () => {
    const header = createHeaderState(() => {});
    header.send({ type: 'mobile' });
    assert.equal(headerSurface(header.snapshot(), true), 'light');
    header.send({ type: 'dismiss' });
    assert.equal(headerSurface(header.snapshot(), true), 'light');
    assert.equal(headerSurface(header.snapshot(), false), 'hero');
    header.send({ type: 'scroll', y: 200, outsideHero: false });
    assert.equal(headerSurface(header.snapshot(), false), 'light');
});
