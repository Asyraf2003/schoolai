import assert from 'node:assert/strict';
import { settledValuesLayout } from './ValuesBrowserSupport.mjs';

export async function galleryResize(page, width, height) {
    await page.setViewportSize({ width, height });
    await settledValuesLayout(page);
    await page.waitForFunction(([w, h]) => {
        const root = document.querySelector('[data-gallery]');
        return +root.dataset.galleryWidth === w && +root.dataset.galleryHeight === h;
    }, [width, height]);
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    await page.waitForFunction(() => {
        const root = document.querySelector('[data-gallery]');
        const trigger = window.ScrollTrigger.getById('gallery-v2');
        const box = root.getBoundingClientRect();
        return !root.hasAttribute('data-gallery-reflow-pending')
            && Math.abs(trigger.start - (box.top + scrollY - innerHeight)) < 2;
    });
}

export async function galleryReady(page) {
    await page.evaluate(() => {
        const root = document.querySelector('[data-gallery]');
        scrollTo(0, root.getBoundingClientRect().top + scrollY - innerHeight);
    });
    await page.waitForFunction(() => !!window.ScrollTrigger?.getById('gallery-v2'));
    await page.evaluate(() => document.fonts.ready);
    await page.waitForFunction(() => document.querySelector('[data-values]').hasAttribute('data-line-ready'));
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    await page.waitForFunction(() => {
        const root = document.querySelector('[data-gallery]');
        const trigger = window.ScrollTrigger.getById('gallery-v2');
        const box = root.getBoundingClientRect();
        return !root.hasAttribute('data-gallery-reflow-pending')
            && Math.abs(trigger.start - (box.top + scrollY - innerHeight)) < 2;
    });
}
export async function galleryOpening(page) {
    return galleryAt(page, +(await page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start')));
}
export async function galleryAt(page, progress) {
    await page.evaluate(p => {
        window.ScrollTrigger.refresh();
        const t = window.ScrollTrigger.getById('gallery-v2');
        scrollTo(0, Math.round(t.start + (t.end - t.start) * p));
        t.update();
    }, progress);
    try {
        await page.waitForFunction(p => Math.abs(window.ScrollTrigger.getById('gallery-v2').progress - p) < .002, progress);
    } catch (error) {
        const state = await page.evaluate(() => {
            const t = window.ScrollTrigger.getById('gallery-v2');
            return { scroll: scrollY, start: t?.start, end: t?.end, progress: t?.progress, viewport: [innerWidth, innerHeight] };
        });
        throw new Error(`Gallery requested ${progress}: ${JSON.stringify(state)}`, { cause: error });
    }
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    await page.waitForFunction(() => [...document.querySelectorAll('[data-gallery-poster]')].every(image => {
        const item = image.parentElement;
        const r = item.getBoundingClientRect();
        const visible = r.bottom > 0 && r.top < innerHeight && r.right > 0 && r.left < innerWidth && +getComputedStyle(item).opacity > .15;
        return !visible || image.complete && image.naturalWidth > 0;
    }));
    return page.evaluate(galleryState);
}
export function galleryState() {
    const rect = e => { const r = e.getBoundingClientRect(); return { x: r.x, y: r.y, width: r.width, height: r.height, bottom: r.bottom }; };
    const root = document.querySelector('[data-gallery]');
    const heading = document.querySelector('[data-gallery-heading]');
    const statement = document.querySelector('[data-gallery-statement]');
    const cardViewport = document.querySelector('[data-values-cards-viewport]');
    const cardClipTop = getComputedStyle(cardViewport).position === 'sticky' ? Math.max(0, cardViewport.getBoundingClientRect().top) : 0;
    const valuesRemaining = Math.max(...[...document.querySelectorAll('[data-values-card-inner]')].map(card => {
        const r = card.getBoundingClientRect();
        return Math.max(0, r.bottom - cardClipTop) / Math.max(1, r.height);
    }));
    const lastStrand = [...document.querySelectorAll('[data-values-line-path]')].at(-1);
    const filterBlur = e => parseFloat(getComputedStyle(e).filter.match(/[\d.]+/)?.[0] ?? 0);
    const toRGB = value => {
        const srgb = value.match(/color\(srgb ([\d.]+) ([\d.]+) ([\d.]+)/);
        return srgb ? srgb.slice(1).map(v => Math.round(Number(v) * 255))
            : value.match(/[\d.]+/g).slice(0, 3).map(Number);
    };
    const background = getComputedStyle(document.querySelector('[data-program-values]')).backgroundColor;
    const backgroundRGB = toRGB(background);
    return {
        scroll: scrollY,
        pinStart: +root.dataset.galleryPinStart,
        formation: +root.dataset.galleryFormation,
        exitStart: +root.dataset.galleryExitStart,
        exitPinned: +root.dataset.galleryExitPinned,
        valuesRemaining, width: innerWidth, height: innerHeight, overflow: document.documentElement.scrollWidth > innerWidth,
        heading: { ...rect(heading), color: getComputedStyle(heading).color, colorRGB: toRGB(getComputedStyle(heading).color),
            size: getComputedStyle(heading).fontSize, visibility: getComputedStyle(heading).visibility,
            valuesSize: getComputedStyle(document.querySelector('[data-values-heading]')).fontSize },
        header: rect(document.querySelector('.gallery__header')),
        background, backgroundRGB,
        typeVisible: getComputedStyle(document.querySelector('.program__type-home')).visibility,
        lineOpacity: +getComputedStyle(document.querySelector('[data-values-line]')).opacity,
        typeOpacity: +getComputedStyle(document.querySelector('.program__type-home')).opacity,
        lastDraw: 1 - parseFloat(getComputedStyle(lastStrand).strokeDashoffset) / lastStrand.getTotalLength(),
        blur: filterBlur(document.querySelector('[data-gallery-composition]')),
        statementColor: getComputedStyle(statement).color,
        statementMist: parseFloat(getComputedStyle(statement).getPropertyValue('--gallery-statement-mist')) || 0,
        items: [...document.querySelectorAll('[data-gallery-item]')].map(e => ({ ...rect(e), opacity: +getComputedStyle(e).opacity,
            column: e.dataset.column, image: e.querySelector('img').naturalWidth })),
        words: [...document.querySelectorAll('[data-gallery-word]')].map(word => ({ opacity: +getComputedStyle(word).opacity, blur: filterBlur(word) })),
        videos: [...document.querySelectorAll('[data-gallery-video]')].map(v => ({ playing: !v.paused, hydrated: v.hasAttribute('src'), time: v.currentTime })),
        gallery: rect(root),
        testimonials: rect(document.querySelector('[data-testimonials]')),
        stories: rect(document.querySelector('[data-stories]')),
        triggers: window.ScrollTrigger?.getAll().filter(t => t.vars.id === 'gallery-v2').length ?? 0,
    };
}
export function galleryFits(state) {
    assert.equal(state.overflow, false);
    assert.equal(state.items.length, 34);
    for (let a = 0; a < state.items.length; a++) for (let b = a + 1; b < state.items.length; b++) {
        const x = state.items[a], y = state.items[b];
        assert.ok(x.x + x.width <= y.x + 1 || y.x + y.width <= x.x + 1
            || x.bottom <= y.y + 1 || y.bottom <= x.y + 1, `overlap ${a}/${b}`);
    }
    assert.ok(state.items.every(i => i.x >= -1 && i.x + i.width <= state.width + 1));
    assert.ok(state.items.filter(i => i.bottom > 0 && i.y < state.height && i.opacity > .15).every(i => i.image > 0), 'visible media retain decoded posters');
    assert.ok(state.videos.filter(v => v.playing).length <= 2);
    assert.ok(state.videos.filter(v => v.hydrated).length <= 2);
}
