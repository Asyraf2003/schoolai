import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test('Hero V2 stays video-only with no article carousel in ' + engine, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(runtime.page, lang);
                for (const width of [390, 1440]) {
                    await runtime.page.setViewportSize({ width, height: 900 });
                    const state = await runtime.page.locator('[data-hero]').evaluate(hero => {
                        const video = hero.querySelector('video[data-video]');
                        const controls = hero.querySelector('[data-hero-controls]');
                        return {
                            slides: hero.querySelectorAll('[data-slide]').length,
                            videos: hero.querySelectorAll('video[data-video]').length,
                            kind: hero.querySelector('[data-slide]')?.dataset.kind,
                            videoSource: video?.querySelector('source')?.dataset.src,
                            poster: video?.getAttribute('poster'),
                            title: hero.querySelectorAll('h1.hero__title').length,
                            previous: hero.querySelectorAll('[data-previous]').length,
                            next: hero.querySelectorAll('[data-next]').length,
                            controlsHidden: !controls || controls.hidden,
                            direction: document.documentElement.dir,
                            videoLoop: video?.loop,
                        };
                    });
                    assert.equal(state.slides, 1, lang + '/' + width + ' slides');
                    assert.equal(state.videos, 1, lang + '/' + width + ' videos');
                    assert.equal(state.kind, 'video');
                    assert.ok(state.videoSource?.endsWith('/site/hero/homepage-opening-v2.mp4'));
                    assert.ok(state.poster?.endsWith('/site/hero/hero-school.webp'));
                    assert.equal(state.title, 1);
                    assert.equal(state.previous, 0);
                    assert.equal(state.next, 0);
                    assert.equal(state.controlsHidden, true);
                    assert.equal(state.direction, lang === 'ar' ? 'rtl' : 'ltr');
                    assert.equal(state.videoLoop, true, 'single opening video loops when JS is active');
                    await runtime.page.screenshot({ path: '/tmp/hero-video-only-' + engine + '-' + lang + '-' + width + '.png' });
                }
            }
            assert.deepEqual(runtime.errors, []);
        } finally {
            await runtime.close();
        }
    });
}
