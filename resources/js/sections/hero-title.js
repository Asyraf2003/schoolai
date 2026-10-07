// Content fitting boundary; no title-specific data or device-specific text copies.
export function mountHeroTitles(root) {
    const titles = [...root.querySelectorAll('.hero__title')];
    let frame;
    let disposed = false;
    function fit() {
        if (disposed) return;
        titles.forEach(title => {
            title.style.removeProperty('font-size');
            const normal = parseFloat(getComputedStyle(title).fontSize);
            const minimum = Math.min(normal, parseFloat(getComputedStyle(document.documentElement).fontSize) * 1.5);
            let low = minimum;
            let high = normal;
            const fits = () => title.getBoundingClientRect().height <= parseFloat(getComputedStyle(title).lineHeight) * 2 + 1;
            if (fits()) return;
            for (let attempt = 0; attempt < 7; attempt++) {
                const size = (low + high) / 2;
                title.style.fontSize = `${size}px`;
                if (fits()) low = size; else high = size;
            }
            title.style.fontSize = `${low}px`;
        });
    }
    const schedule = () => { cancelAnimationFrame(frame); frame = requestAnimationFrame(fit); };
    const observer = 'ResizeObserver' in window ? new ResizeObserver(schedule) : null;
    root.querySelectorAll('.hero__copy').forEach(copy => observer?.observe(copy));
    window.addEventListener('resize', schedule);
    document.fonts?.ready.then(schedule);
    schedule();
    return () => { disposed = true; cancelAnimationFrame(frame); observer?.disconnect(); window.removeEventListener('resize', schedule); };
}
