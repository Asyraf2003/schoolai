import assert from 'node:assert/strict';
import { completeSnapshot, assertCompleteSnapshot } from './home-complete-journey.mjs';

export async function traverseNativeHomepage(page, client, requestLog, requestIndex = requestLog.length) {
    const start = await completeSnapshot(page);
    assertCompleteSnapshot(start);
    const passes = [];
    for (const direction of ['down', 'up', 'down']) {
        const distance = await page.evaluate(() => document.documentElement.scrollHeight - innerHeight);
        await page.evaluate(() => {
            window.nativeFrames = { samples: [], running: true, last: null };
            const sample = time => {
                const state = window.nativeFrames;
                if (!state.running) return;
                if (state.last !== null) state.samples.push({ time, gap: time - state.last, y: scrollY });
                state.last = time;
                requestAnimationFrame(sample);
            };
            requestAnimationFrame(sample);
        });
        const viewport = page.viewportSize();
        await client.send('Input.synthesizeScrollGesture', {
            x: viewport.width / 2, y: Math.min(650, viewport.height - 100),
            yDistance: direction === 'down' ? -distance : distance,
            speed: 1800, preventFling: true,
            gestureSourceType: viewport.width < 640 ? 'touch' : 'mouse',
        });
        const frames = await page.evaluate(() => {
            window.nativeFrames.running = false;
            return { samples: window.nativeFrames.samples, y: scrollY, end: document.documentElement.scrollHeight - innerHeight };
        });
        const target = direction === 'down' ? frames.end : 0;
        assert.ok(Math.abs(frames.y - target) <= 2, `native ${direction} must reach the full journey endpoint`);
        const state = await completeSnapshot(page);
        assert.deepEqual(state.sections, start.sections);
        assert.ok(state.images.every(image => image.ready));
        assert.equal(state.canvas, start.canvas); assert.equal(state.cursor, start.cursor);
        for (const name of ['lateLoads', 'lateSources', 'lateInitialization']) assert.deepEqual(state.proof[name], []);
        const gaps = frames.samples.map(sample => sample.gap).sort((a, b) => a - b);
        passes.push({ direction, input: viewport.width < 640 ? 'native touch gesture' : 'native mouse gesture', speed: 1800,
            ...frames, state, gaps, median: gaps[Math.floor(gaps.length / 2)], p95: gaps[Math.floor(gaps.length * .95)], worst: gaps.at(-1) });
    }
    const requests = requestLog.slice(requestIndex);
    const blockers = requests.filter(request => ['script', 'stylesheet', 'font', 'image', 'fetch', 'xhr'].includes(request.type));
    assert.deepEqual(blockers, []);
    return { start, passes, requests, blockers, method: 'native compositor scroll; individual RAF intervals; no programmatic viewport jumps' };
}
