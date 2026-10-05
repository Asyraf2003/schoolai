// Visual amplitude follows the existing Hero audio status; this module owns no audio.
export function advanceAmplitude(current, enabled, seconds) {
    return Math.max(0, Math.min(1, current + (enabled ? 1 : -1) * seconds));
}

export function wavePoints(size, amplitude, phase) {
    return Array.from({ length: 33 }, (_, index) => {
        const position = index / 32;
        const edge = Math.max(0, Math.min(1, (0.5 - Math.abs(position - 0.5)) / 0.25));
        const envelope = 0.3 + 0.7 * (1 - Math.cos(Math.PI * edge)) / 2;
        return [size * (0.3 + 0.4 * position),
            size / 2 + Math.sin(phase * 8 + position * 7) * size * 0.14 * amplitude * envelope];
    });
}

export function mountHeaderSound(button) {
    const canvas = button.querySelector('[data-audio-wave]');
    const context = canvas?.getContext('2d');
    if (!context) return { update() {}, dispose() {} };
    button.dataset.waveReady = 'true';
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    let enabled = false;
    let active = false;
    let suspended = false;
    let disposed = false;
    let amplitude = 0;
    let phase = 0;
    let size = 0;
    let scale = 1;
    let color = '#fff';
    let frame = 0;
    let previous = 0;
    let treatment = '';
    function draw() {
        context.setTransform(scale, 0, 0, scale, 0, 0);
        context.clearRect(0, 0, size, size);
        context.strokeStyle = color;
        context.lineWidth = size * 0.05;
        context.lineCap = 'round';
        context.beginPath();
        wavePoints(size, amplitude, phase).forEach(([x, y], index) => {
            if (index === 0) context.moveTo(x, y);
            else context.lineTo(x, y);
        });
        context.stroke();
    }
    function tick(time) {
        frame = 0;
        const delta = previous ? Math.min((time - previous) / 1000, 0.05) : 0;
        previous = time;
        amplitude = advanceAmplitude(amplitude, enabled, delta);
        phase += delta * 0.6;
        draw();
        if (enabled || amplitude > 0) frame = requestAnimationFrame(tick);
        else previous = 0;
    }
    function refresh() {
        cancelAnimationFrame(frame);
        frame = 0;
        previous = 0;
        if (disposed || !active || suspended || document.hidden) return;
        size = canvas.getBoundingClientRect().width;
        if (!size) return;
        scale = Math.min(window.devicePixelRatio || 1, 2);
        canvas.width = canvas.height = Math.round(size * scale);
        color = getComputedStyle(button).getPropertyValue('--header-color').trim();
        if (preference.matches) amplitude = enabled ? 1 : 0;
        draw();
        if (!preference.matches && (enabled || amplitude > 0)) frame = requestAnimationFrame(tick);
    }
    const observer = new ResizeObserver(refresh);
    observer.observe(button);
    const hide = () => { suspended = true; refresh(); };
    const show = () => { suspended = false; refresh(); };
    document.addEventListener('visibilitychange', refresh);
    window.addEventListener('pagehide', hide);
    window.addEventListener('pageshow', show);
    window.addEventListener('resize', refresh, { passive: true });
    preference.addEventListener('change', refresh);
    return {
        update(status, header) {
            const nextActive = status.available && !header.desktop && !header.concealed && header.panel !== 'language';
            const nextTreatment = `${header.scrolled}:${header.navigationOpen ?? (header.mobileOpen || header.panel !== null)}`;
            if (enabled === status.enabled && active === nextActive && treatment === nextTreatment) return;
            enabled = status.enabled;
            active = nextActive;
            treatment = nextTreatment;
            refresh();
        },
        dispose() {
            disposed = true;
            cancelAnimationFrame(frame);
            observer.disconnect();
            document.removeEventListener('visibilitychange', refresh);
            window.removeEventListener('pagehide', hide);
            window.removeEventListener('pageshow', show);
            window.removeEventListener('resize', refresh);
            preference.removeEventListener('change', refresh);
            delete button.dataset.waveReady;
        },
    };
}
