import test from 'node:test';
import assert from 'node:assert/strict';
import { mountTestimonials } from '../../resources/js/sections/testimonials.js';

function fixture(t) {
    const root = new EventTarget();
    const motion = new EventTarget();
    const document = new EventTarget();
    const navigations = [];
    t.mock.timers.enable({ apis: ['setTimeout'] });
    globalThis.document = document;
    globalThis.matchMedia = () => motion;
    globalThis.window = { location: { assign: href => navigations.push(href) } };
    const controller = mountTestimonials(root);
    t.after(() => {
        controller.dispose();
        delete globalThis.document;
        delete globalThis.matchMedia;
        delete globalThis.window;
    });

    function card(link) {
        const attributes = new Set();
        const target = {
            setAttribute: name => attributes.add(name),
            removeAttribute: name => attributes.delete(name),
            closest: selector => selector === '[data-testimonial-card]' ? target : link,
        };
        const dispatch = (type, fields) => {
            const event = new Event(type, { cancelable: true });
            Object.defineProperty(event, 'target', { value: target });
            Object.assign(event, fields);
            root.dispatchEvent(event);
            return event;
        };
        return {
            lit: () => attributes.has('data-testimonial-lit'),
            click: fields => dispatch('click', { detail: 1, button: 0, ...fields }),
            pointerdown: pointerType => dispatch('pointerdown', { pointerType }),
        };
    }
    return { card, navigations, motion, document, controller };
}

const href = 'https://www.instagram.com/p/TEST_FIXTURE/';

test('touch keeps the effect for 700 ms and follows a linked testimonial once', t => {
    const { card, navigations } = fixture(t);
    const linked = card({ href, isConnected: true });
    linked.pointerdown('touch');
    assert.equal(linked.click().defaultPrevented, true, 'MouseEvent fallback uses the preceding touch pointer');
    assert.equal(linked.lit(), true);
    t.mock.timers.tick(699);
    assert.deepEqual(navigations, []);
    assert.equal(linked.click({ pointerType: 'touch' }).defaultPrevented, true);
    t.mock.timers.tick(1);
    assert.equal(linked.lit(), false);
    assert.deepEqual(navigations, [href]);
});

test('sample touch lights without navigation and cancels an earlier tap', t => {
    const { card, navigations } = fixture(t);
    const linked = card({ href, isConnected: true });
    const sample = card();
    linked.click({ pointerType: 'touch' });
    t.mock.timers.tick(400);
    assert.equal(sample.click({ pointerType: 'touch' }).defaultPrevented, false);
    assert.equal(linked.lit(), false);
    assert.equal(sample.lit(), true);
    t.mock.timers.tick(700);
    assert.equal(sample.lit(), false);
    assert.deepEqual(navigations, []);
});

test('keyboard, mouse, modified clicks and reduced motion preserve native navigation', t => {
    const { card, navigations, motion } = fixture(t);
    const linked = card({ href, isConnected: true });
    for (const fields of [{ detail: 0 }, { pointerType: 'mouse' }, { pointerType: 'touch', ctrlKey: true }]) {
        assert.equal(linked.click(fields).defaultPrevented, false);
        assert.equal(linked.lit(), false);
    }
    motion.matches = true;
    assert.equal(linked.click({ pointerType: 'touch' }).defaultPrevented, false);
    t.mock.timers.tick(700);
    assert.deepEqual(navigations, []);
});

test('hidden pages, pagehide, preference changes and disposal cancel pending navigation', t => {
    const { card, navigations, motion, document, controller } = fixture(t);
    const linked = card({ href, isConnected: true });
    for (const cancel of [
        () => { document.hidden = true; document.dispatchEvent(new Event('visibilitychange')); },
        () => controller.suspend(),
        () => motion.dispatchEvent(new Event('change')),
        () => controller.dispose(),
    ]) {
        linked.click({ pointerType: 'touch' });
        assert.equal(linked.lit(), true);
        cancel();
        assert.equal(linked.lit(), false);
        t.mock.timers.tick(700);
    }
    assert.equal(linked.click({ pointerType: 'touch' }).defaultPrevented, false);
    assert.deepEqual(navigations, []);
});
