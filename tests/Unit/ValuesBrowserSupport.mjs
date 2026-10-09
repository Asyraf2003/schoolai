// Resize can rebuild the line timeline and GSAP then performs its debounced
// global refresh. Wait for both owners to stop changing layout before issuing
// the next scroll command; otherwise that refresh can restore an earlier scroll.
export async function settledValuesLayout(page) {
    await page.evaluate(() => new Promise(resolve => {
        let changed = performance.now();
        let previous;
        const invalidate = () => { changed = performance.now(); };
        window.addEventListener('resize', invalidate);
        window.ScrollTrigger?.addEventListener('refresh', invalidate);
        const sample = now => {
            const root = document.querySelector('[data-values]');
            const track = root.querySelector('[data-values-cards-track]');
            const trigger = window.ScrollTrigger?.getById('values-line');
            const key = [innerWidth, innerHeight, root.offsetTop, root.offsetHeight,
                track.offsetTop, track.offsetHeight, document.documentElement.scrollHeight,
                trigger?.start, trigger?.end].join(':');
            if (key !== previous) { previous = key; changed = now; }
            if (now - changed < 300) return requestAnimationFrame(sample);
            window.removeEventListener('resize', invalidate);
            window.ScrollTrigger?.removeEventListener('refresh', invalidate);
            resolve();
        };
        requestAnimationFrame(sample);
    }));
}
