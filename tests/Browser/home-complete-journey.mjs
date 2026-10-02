import assert from 'node:assert/strict';

export async function instrumentCompleteJourney(context) {
    await context.addInitScript(() => {
        window.completeProof = { events: [], lateLoads: [], lateSources: [], lateInitialization: [], tasks: [], waiting: [] };
        const unlocked = () => document.documentElement.dataset.homeScrollGate === 'unlocked';
        const load = HTMLMediaElement.prototype.load;
        HTMLMediaElement.prototype.load = function (...args) {
            if (unlocked()) window.completeProof.lateLoads.push(this.currentSrc || this.src);
            return load.apply(this, args);
        };
        const sourceObserver = new MutationObserver(entries => {
            if (!unlocked()) return;
            for (const entry of entries) {
                if (entry.target.matches('img,video,source') && entry.oldValue !== entry.target.getAttribute(entry.attributeName)) {
                    window.completeProof.lateSources.push({ tag: entry.target.tagName, source: entry.target.getAttribute(entry.attributeName) });
                }
                if (entry.attributeName.endsWith('-ready') && entry.oldValue === null) {
                    window.completeProof.lateInitialization.push({ id: entry.target.id, attribute: entry.attributeName });
                }
            }
        });
        sourceObserver.observe(document, { subtree: true, attributes: true, attributeOldValue: true,
            attributeFilter: ['src', 'srcset', 'data-hero-controller-ready', 'data-program-ready', 'data-values-ready', 'data-gallery-ready', 'data-testimonial-ready'] });
        for (const name of ['schoolai:home-preparation', 'schoolai:opening-progress', 'schoolai:opening-handoff', 'schoolai:first-journey-ready']) {
            document.addEventListener(name, event => window.completeProof.events.push({ name, detail: event.detail, time: performance.now(), gate: document.documentElement.dataset.homeScrollGate }));
        }
        try { new PerformanceObserver(list => {
            for (const entry of list.getEntries()) if (unlocked()) window.completeProof.tasks.push({ start: entry.startTime, duration: entry.duration });
        }).observe({ type: 'longtask', buffered: false }); } catch {}
        document.addEventListener('waiting', event => {
            const rect = event.target.getBoundingClientRect();
            const visual = event.target.closest('[data-vision-visual]'), story = visual?.closest('[data-vision-story]');
            const shown = !story?.classList.contains('is-enhanced') || story.dataset.visionMediaRange?.split(',').includes(visual.dataset.visionVisual);
            if (unlocked() && shown && rect.top < innerHeight && rect.bottom > 0) window.completeProof.waiting.push({ src: event.target.currentSrc, time: performance.now(), ready: event.target.readyState });
        }, true);
    });
}

export async function completeSnapshot(page) {
    return page.evaluate(() => ({
        root: { ...document.documentElement.dataset }, y: scrollY,
        environment: { dpr: devicePixelRatio, visibility: document.visibilityState, width: innerWidth, height: innerHeight,
            finePointer: matchMedia('(pointer: fine)').matches, reduced: matchMedia('(prefers-reduced-motion: reduce)').matches },
        images: [...document.images].map(image => ({ source: image.currentSrc || image.src, ready: image.complete && image.naturalWidth > 0 })),
        previews: [...document.querySelectorAll('[data-vision-video-preview],[data-gallery-video-preview],[data-hero-video]')].map(video => ({
            source: video.currentSrc, state: video.dataset.visionVideoState || video.dataset.galleryVideoState || video.dataset.heroVideoState
                || (video.matches('[data-hero-video]')
                    ? (video.closest('[data-hero-slider]')?.dataset.heroPreparationFallback === 'true' ? 'poster-ready' : 'frame-ready') : 'pending'),
            width: video.videoWidth, ready: video.readyState, poster: video.poster, time: video.currentTime,
            buffered: Array.from({ length: video.buffered.length }, (_, index) => [video.buffered.start(index), video.buffered.end(index)]),
        })),
        sections: [...document.querySelectorAll('main section,.site-footer')].map(element => ({ id: element.id, height: element.getBoundingClientRect().height })),
        controllers: {
            hero: document.querySelector('[data-hero-slider]').dataset.heroControllerReady,
            vision: document.querySelector('[data-vision-story]').dataset.visionState,
            program: document.querySelector('[data-program-kinetic]').dataset.programReady,
            values: document.querySelector('[data-values-story]').dataset.valuesReady,
            gallery: document.querySelector('[data-gallery-story]').dataset.galleryReady,
            testimonials: document.querySelector('[data-testimonial-wall]').dataset.testimonialReady,
            articles: document.querySelector('[data-editorial-heading]').className,
        },
        inert: [...document.querySelectorAll('main > [inert],.site-footer[inert]')].length,
        fonts: [...document.fonts].map(font => ({ family: font.family, status: font.status, weight: font.weight })),
        cursor: document.querySelectorAll('[data-home-cursor]').length, canvas: document.querySelectorAll('canvas').length,
        overflow: document.documentElement.scrollWidth - innerWidth, proof: window.completeProof,
    }));
}

export function assertCompleteSnapshot(state) {
    assert.equal(state.root.homeScrollGate, 'unlocked'); assert.equal(state.root.homeOpeningProgress, '100');
    assert.equal(state.root.homePreparedThrough, 'geometry'); assert.equal(state.inert, 0);
    assert.ok(state.images.every(image => image.ready));
    assert.ok(state.previews.every(video => ['frame-ready', 'poster-ready'].includes(video.state)));
    assert.ok(state.previews.every(video => video.state === 'poster-ready' ? video.poster : video.width > 0));
    for (const unit of ['homeFontsReady', 'homeImagesReady', 'homeMediaReady', 'homeGeometryReady']) assert.equal(state.root[unit], 'true');
    assert.equal(state.controllers.hero, 'true'); assert.equal(state.controllers.testimonials, 'prepared');
    for (const unit of ['vision','program','values','gallery']) assert.ok(['prepared','static-ready','static-fallback','gsap'].includes(state.controllers[unit]));
    assert.equal(state.proof.events.filter(event => event.name === 'schoolai:home-preparation').length, 15);
    const hundred = state.proof.events.find(event => event.name === 'schoolai:opening-progress' && event.detail.value === 100);
    const handoff = state.proof.events.find(event => event.name === 'schoolai:opening-handoff');
    const unlock = state.proof.events.filter(event => event.name === 'schoolai:first-journey-ready');
    assert.equal(unlock.length, 1); assert.equal(hundred.gate, 'locked'); assert.equal(handoff.gate, 'locked');
    assert.ok(hundred.time <= handoff.time && handoff.time <= unlock[0].time);
    for (const event of state.proof.events.filter(event => event.name === 'schoolai:opening-progress')) {
        const settled = Object.values(event.detail.units).filter(value => ['PREPARED', 'STATIC_FALLBACK'].includes(value)).length;
        assert.equal(event.detail.value, Math.floor(settled * 100 / 15));
    }
}

export async function traverseCompleteHomepage(page, requestLog, requestIndex = requestLog.length) {
    const start = await completeSnapshot(page); assertCompleteSnapshot(start);
    const passes = [];
    for (const direction of ['down', 'up', 'down']) {
        const frames = await page.evaluate(async direction => {
            const end = Math.max(0, document.documentElement.scrollHeight - innerHeight);
            const step = Math.max(180, innerHeight * .65), gaps = [], positions = [];
            const target = direction === 'down' ? end : 0;
            let position = scrollY, last = performance.now();
            while (Math.abs(target - position) > 1) {
                position = direction === 'down' ? Math.min(target, position + step) : Math.max(target, position - step);
                scrollTo({ top: position, behavior: 'instant' });
                await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
                const now = performance.now(); gaps.push(now - last); last = now;
                positions.push(position);
            }
            await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
            return { gaps, positions, y: scrollY, end, target };
        }, direction);
        assert.ok(Math.abs(frames.y - frames.target) <= 2);
        const state = await completeSnapshot(page);
        assert.ok(state.images.every(image => image.ready)); assert.deepEqual(state.sections, start.sections);
        assert.equal(state.canvas, start.canvas); assert.equal(state.cursor, start.cursor);
        assert.deepEqual(state.proof.lateLoads, []); assert.deepEqual(state.proof.lateSources, []);
        assert.deepEqual(state.proof.lateInitialization, []);
        passes.push({ direction, ...frames, state });
    }
    const requests = requestLog.slice(requestIndex);
    const blockers = requests.filter(request => ['script', 'stylesheet', 'font', 'image', 'fetch', 'xhr'].includes(request.type));
    assert.deepEqual(blockers, []);
    return { start, passes, requests, blockers };
}
