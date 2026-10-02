import assert from 'node:assert/strict';
import { mkdirSync, writeFileSync } from 'node:fs';

const { chromium, webkit } = await import(process.env.SCHOOLAI_BROWSER_MODULE);
const engine = process.env.SCHOOLAI_ENGINE || 'chromium';
const browser = engine === 'webkit'
    ? await webkit.launch({ executablePath: process.env.SCHOOLAI_WEBKIT, headless: true })
    : await chromium.connectOverCDP(process.env.SCHOOLAI_CHROME_CDP);
const base = process.env.SCHOOLAI_BASE || 'http://127.0.0.1:8021';
const directory = process.env.SCHOOLAI_PROOF_DIR;
const widths = (process.env.SCHOOLAI_WIDTHS || '360,390,640,768,1023,1024,1180,1181,1279,1280,1440,1536').split(',').map(Number);
const locales = (process.env.SCHOOLAI_LOCALES || 'id,en,ar').split(',');
const motions = (process.env.SCHOOLAI_MOTIONS || 'no-preference,reduce').split(',');
mkdirSync(directory, { recursive: true });
const records = [];
async function snapshot(page) {
    return page.evaluate(() => {
        const root = document.querySelector('[data-vision-story]');
        const rect = root.getBoundingClientRect();
        return { state: root.dataset.visionState, enhanced: root.classList.contains('is-enhanced'),
            lang: document.documentElement.lang, dir: document.documentElement.dir,
            overflow: document.documentElement.scrollWidth - innerWidth,
            start: rect.top + scrollY, end: rect.bottom + scrollY - innerHeight,
            height: rect.height, y: scrollY, range: root.dataset.visionMediaRange,
            media: [...root.querySelectorAll('video')].filter(v => v.matches('[data-vision-video-preview]')).map(v => ({
                state: v.dataset.visionVideoState, src: v.currentSrc, ready: v.readyState, paused: v.paused,
                frames: v.getVideoPlaybackQuality?.().totalVideoFrames, time: v.currentTime,
                buffer: Array.from({ length: v.buffered.length }, (_, i) => [v.buffered.start(i), v.buffered.end(i)]),
            })),
            posters: [...root.querySelectorAll('[data-vision-preview-poster]')].map(i => i.complete && i.naturalWidth > 0),
        };
    });
}
try {
    for (const width of widths) for (const locale of locales) for (const motion of motions) {
        const context = await browser.newContext({ viewport: { width, height: 900 }, deviceScaleFactor: 1,
            reducedMotion: motion, hasTouch: width < 1024, isMobile: width < 640 });
        const errors = [];
        try {
            if (locale !== 'id') {
                const response = await context.request.get(base);
                const token = (await response.text()).match(/name="_token" value="([^"]+)"/)[1];
                await context.request.post(base + '/bahasa/' + locale, { form: { _token: token }, headers: { referer: base } });
            }
            const page = await context.newPage();
            page.on('pageerror', error => errors.push(error.message));
            const client = engine === 'chromium' ? await context.newCDPSession(page) : null;
            if (client) await client.send('Network.clearBrowserCache');
            if (client && process.env.SCHOOLAI_TRACE) await client.send('Tracing.start', {
                categories: 'devtools.timeline,blink.user_timing,gpu,media,cc,disabled-by-default-devtools.timeline.frame,disabled-by-default-gpu.service',
                transferMode: 'ReturnAsStream',
            });
            await page.addInitScript(() => {
                window.visionProof = { lateSources: [], mediaEvents: [], longTasks: [], lcp: [], unlock: null };
                new PerformanceObserver(list => visionProof.longTasks.push(...list.getEntries().map(e => ({ start: e.startTime, duration: e.duration })))).observe({ type: 'longtask', buffered: true });
                new PerformanceObserver(list => visionProof.lcp.push(...list.getEntries().map(e => ({ start: e.startTime, url: e.url })))).observe({ type: 'largest-contentful-paint', buffered: true });
                for (const name of ['waiting', 'stalled', 'playing', 'error']) document.addEventListener(name, event => {
                    if (event.target.matches?.('[data-vision-video-preview]')) visionProof.mediaEvents.push({
                        name, time: performance.now(), y: scrollY, state: event.target.dataset.visionVideoState,
                        ready: event.target.readyState, source: event.target.currentSrc,
                    });
                }, true);
                document.addEventListener('schoolai:first-journey-ready', () => {
                    visionProof.unlock = performance.now();
                    new MutationObserver(entries => {
                        for (const entry of entries) if (entry.target.matches('[data-vision-video-preview]')) {
                            visionProof.lateSources.push(entry.target.currentSrc);
                        }
                    }).observe(document.querySelector('[data-vision-story]'), { subtree: true, attributes: true, attributeFilter: ['src'] });
                });
            });
            await page.goto(base, { waitUntil: 'domcontentloaded' });
            await page.bringToFront();
            assert.ok(await page.locator('[data-hero-slider]').isVisible());
            assert.ok(await page.locator('#navbar').isVisible());
            await page.waitForFunction(() => document.documentElement.dataset.homeScrollGate === 'unlocked', null, { timeout: 120000 });
            const initial = await snapshot(page);
            assert.equal(initial.lang, locale);
            assert.ok(initial.overflow <= 1);
            assert.ok(initial.posters.every(Boolean));
            assert.equal(initial.enhanced, width >= 1024 && motion !== 'reduce');
            assert.ok(initial.media.every(v => v.state === (motion === 'reduce' ? 'poster-ready' : 'frame-ready')));
            const passes = [];
            for (const direction of [1, -1, 1]) {
                await page.evaluate(() => {
                    visionProof.frames = []; visionProof.last = null;
                    const sample = time => {
                        if (visionProof.last !== null) visionProof.frames.push({ time, gap: time - visionProof.last, y: scrollY });
                        visionProof.last = time; visionProof.raf = requestAnimationFrame(sample);
                    };
                    visionProof.raf = requestAnimationFrame(sample);
                });
                const y = await page.evaluate(() => scrollY);
                const target = direction === 1 ? initial.end : 0;
                if (client && width < 640) {
                    for (let attempt = 0; attempt < 40; attempt++) {
                        const remaining = target - await page.evaluate(() => scrollY);
                        if (Math.abs(remaining) < 3) break;
                        const sign = Math.sign(remaining), start = sign > 0 ? 650 : 200;
                        const distance = Math.min(360, Math.abs(remaining) + 15);
                        await client.send('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x: width / 2, y: start }] });
                        const steps = Math.ceil(distance / 36);
                        for (let step = 1; step <= steps; step++) {
                            await client.send('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x: width / 2, y: start - sign * distance * step / steps }] });
                            await page.waitForTimeout(30);
                        }
                        await page.waitForTimeout(150);
                        await client.send('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
                        await page.waitForTimeout(50);
                    }
                } else if (client) await client.send('Input.synthesizeScrollGesture', {
                    x: width / 2, y: 450, yDistance: y - target, speed: 1200, preventFling: true,
                    gestureSourceType: 'mouse',
                });
                else {
                    await page.mouse.move(width / 2, 650);
                    for (let distance = Math.abs(target - y); distance > 0; distance -= 120) {
                        await page.mouse.wheel(0, direction * Math.min(120, distance));
                        await page.waitForTimeout(100);
                    }
                }
                const frames = await page.evaluate(() => { cancelAnimationFrame(visionProof.raf); return visionProof.frames; });
                const state = await snapshot(page);
                assert.ok(Math.abs(state.y - target) < 3, `native input reaches scoped endpoint: width=${width}, direction=${direction}, actual=${state.y}, target=${target}, height=${state.height}/${initial.height}`);
                assert.ok(Math.abs(state.height - initial.height) < 1, 'no first-entry geometry change');
                assert.deepEqual(state.media.map(v => v.src), initial.media.map(v => v.src));
                const gaps = frames.filter(f => f.y >= initial.start && f.y <= initial.end).map(f => f.gap).sort((a, b) => a - b);
                passes.push({ direction, state, frames, p95: gaps[Math.floor(gaps.length * .95)], worst: gaps.at(-1) });
            }
            const proof = await page.evaluate(() => ({ unlock: visionProof.unlock, lateSources: visionProof.lateSources, mediaEvents: visionProof.mediaEvents, longTasks: visionProof.longTasks, lcp: visionProof.lcp, paints: performance.getEntriesByType('paint').map(e => ({ name: e.name, start: e.startTime })) }));
            assert.deepEqual(proof.lateSources, []); assert.deepEqual(errors, []);
            if (client && process.env.SCHOOLAI_TRACE) {
                const done = new Promise(resolve => client.once('Tracing.tracingComplete', resolve));
                await client.send('Tracing.end'); const { stream } = await done;
                let raw = '';
                while (true) {
                    const chunk = await client.send('IO.read', { handle: stream }); raw += chunk.data;
                    if (chunk.eof) break;
                }
                await client.send('IO.close', { handle: stream });
                writeFileSync(`${directory}/trace-${records.length}.json`, raw);
            }
            await page.screenshot({ path: `${directory}/${width}-${locale}-${motion}.png` });
            records.push({ width, locale, motion, engine, browser: browser.version(), initial, passes, proof, errors });
            writeFileSync(`${directory}/results.json`, JSON.stringify(records, null, 2));
            console.log(JSON.stringify({ width, locale, motion, maxima: passes.map(p => p.worst), unlock: proof.unlock }));
        } catch (error) {
            const page = context.pages()[0];
            const failure = { width, locale, motion, engine, error: error.message,
                state: page ? await snapshot(page).catch(() => null) : null };
            writeFileSync(`${directory}/failure.json`, JSON.stringify(failure, null, 2));
            if (page) await page.screenshot({ path: `${directory}/failure.png` }).catch(() => {});
            throw error;
        } finally { await context.close(); }
    }
} finally { await browser.close(); }
