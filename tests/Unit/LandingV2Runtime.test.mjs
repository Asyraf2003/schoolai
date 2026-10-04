import test from 'node:test';
import assert from 'node:assert/strict';
import { createHeaderState } from '../../resources/js/sections/header-state.js';
import { createHeroState } from '../../resources/js/sections/hero-state.js';

test('Header resets panels at viewport changes and keeps open/focused navigation visible', () => {
    const header = createHeaderState(() => {});
    header.send({ type: 'mobile' });
    assert.equal(header.snapshot().mobileOpen, true);
    header.send({ type: 'viewport', desktop: true });
    assert.equal(header.snapshot().mobileOpen, false);
    header.send({ type: 'scroll', y: 400, outsideHero: true });
    assert.equal(header.snapshot().concealed, true);
    header.send({ type: 'panel', id: 'education', open: true });
    header.send({ type: 'scroll', y: 500, outsideHero: true });
    assert.equal(header.snapshot().concealed, false);
    header.send({ type: 'panel', id: 'gallery', open: true });
    header.send({ type: 'panel', id: 'education', open: false });
    assert.equal(header.snapshot().panel, 'gallery');
    header.send({ type: 'dismiss' });
    header.send({ type: 'scroll', y: 600, outsideHero: true, focused: true });
    assert.equal(header.snapshot().concealed, false);
});

test('Hero wraps manual navigation but never auto-advances in reduced motion', () => {
    let rendered;
    const hero = createHeroState({ count: 3, reduced: true, render: value => { rendered = value; } });
    hero.send({ type: 'advance' });
    assert.equal(rendered.index, 0);
    assert.equal(rendered.canPlay, false);
    hero.send({ type: 'step', delta: -1 });
    assert.equal(rendered.index, 2);
    hero.send({ type: 'audio' });
    assert.equal(rendered.canPlay, true);
    assert.equal(rendered.canAdvance, false);
    hero.send({ type: 'environment', values: { visible: false } });
    assert.equal(rendered.canPlay, false);
    hero.send({ type: 'audio-blocked' });
    assert.equal(rendered.audio, false);
});

test('Hero stops for focus, offscreen and page suspension without losing user pause', () => {
    let rendered;
    const hero = createHeroState({ count: 2, render: value => { rendered = value; } });
    for (const values of [{ focused: true }, { focused: false, inViewport: false }, { inViewport: true, suspended: true }]) {
        hero.send({ type: 'environment', values });
        hero.send({ type: 'advance' });
        assert.equal(rendered.index, 0);
        assert.equal(rendered.canPlay, false);
    }
    hero.send({ type: 'environment', values: { suspended: false } });
    hero.send({ type: 'advance' });
    assert.equal(rendered.index, 1);
    hero.send({ type: 'pause' });
    hero.send({ type: 'environment', values: { visible: true } });
    assert.equal(rendered.canPlay, false);
});


test('Header keeps white text while Hero remains behind it', () => {
    const header = createHeaderState(() => {});
    header.send({ type: 'scroll', y: 700, outsideHero: false });
    assert.equal(header.snapshot().scrolled, false);
    header.send({ type: 'scroll', y: 900, outsideHero: true });
    assert.equal(header.snapshot().scrolled, true);
    header.send({ type: 'scroll', y: 850, outsideHero: false });
    assert.equal(header.snapshot().scrolled, false);
});
