import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test('Program heading reveals fully then independently shifts at the same speed in ' + engine,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            try {
                for (const lang of ['id', 'en', 'ar']) {
                    await locale(runtime.page, lang);
                    for (const width of [390, 1440]) {
                        const page = runtime.page;
                        await page.setViewportSize({ width, height: 900 });
                        // One-shot heading animation: each viewport needs a fresh page.
                        await page.reload({ waitUntil: 'domcontentloaded' });
                        await page.evaluate(() => scrollTo(0, 0));
                        await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.headingState === 'idle');
                        const hasTwoLines = await page.locator('[data-program-heading]').evaluate(h => h.children.length === 2);

                        // Record every state transition before the browser receives
                        // the trigger. Polling an ephemeral 760ms phase is racy on
                        // busy WebKit CI runners and cannot prove ordering.
                        await page.evaluate(() => {
                            const heading = document.querySelector('[data-program-heading]');
                            window.programHeadingEvents = [];
                            const capture = () => {
                                const lines = [...heading.querySelectorAll('[data-program-heading-line]')];
                                window.programHeadingEvents.push({
                                    state: heading.dataset.headingState,
                                    at: performance.now(),
                                    x: new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41,
                                    lines: lines.map(el => ({
                                        opacity: +getComputedStyle(el).opacity,
                                        y: new DOMMatrix(getComputedStyle(el).transform).m42,
                                    })),
                                    durations: heading.getAnimations({ subtree: true })
                                        .map(animation => animation.effect.getTiming().duration),
                                });
                            };
                            new MutationObserver(capture).observe(heading, {
                                attributes: true, attributeFilter: ['data-heading-state'],
                            });
                            scrollTo(0, heading.getBoundingClientRect().top + scrollY - innerHeight * .35);
                            // Dispatch after native scrolling to cover WebKit's
                            // async scroll-event delivery without changing runtime code.
                            dispatchEvent(new Event('scroll'));
                        });
                        await page.evaluate(() => scrollTo(0, 0));
                        await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.headingState === 'complete');

                        const result = await page.locator('[data-program-heading]').evaluate(heading => ({
                            x: new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41,
                            firstX: new DOMMatrix(getComputedStyle(heading.firstElementChild).transform).m41,
                            lines: [...heading.querySelectorAll('[data-program-heading-line]')].map(el => ({
                                opacity: +getComputedStyle(el).opacity,
                                y: new DOMMatrix(getComputedStyle(el).transform).m42,
                            })),
                            events: window.programHeadingEvents,
                        }));
                        const states = result.events.map(event => event.state);
                        assert.deepEqual(states, hasTwoLines
                            ? ['revealing', 'shifting', 'complete']
                            : ['revealing', 'complete'], lang + '/' + width + ' phase sequence');
                        assert.ok(result.events[0].durations.every(value => value === 760),
                            'reveal duration is 760ms');
                        if (hasTwoLines) {
                            const shift = result.events[1];
                            assert.ok(shift.lines.every(line => line.opacity === 1 && Math.abs(line.y) < .1),
                                'both lines fully revealed before movement');
                            assert.ok(shift.durations.every(value => value === 760),
                                'shift duration equals reveal');
                            assert.ok(shift.at - result.events[0].at >= 720,
                                'horizontal motion never starts before reveal completion');
                            assert.ok(Math.abs(result.events[0].x) < .1,
                                'second line remains unshifted during reveal');
                            assert.ok(result.x * (lang === 'ar' ? -1 : 1) > 1,
                                lang + ': second line travels inward');
                        } else {
                            assert.equal(result.x, 0, 'single Arabic line never shifts');
                        }
                        assert.equal(result.firstX, 0);
                        assert.ok(result.lines.every(line => line.opacity === 1 && Math.abs(line.y) < .1));
                    }
                }
                assert.deepEqual(runtime.errors, []);
            } finally {
                await runtime.close();
            }
        });
}
