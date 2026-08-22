function clamp(value) {
    return Math.max(0, Math.min(1, value));
}

function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

export function initialiseDesktopContinuity(heading, section, desktop) {
    const depth = section.querySelector('[data-depth-gallery]');
    let frame = 0;
    let destroyed = false;

    heading.classList.add('gallery-heading-motion--scroll-linked');

    function clearDesktopState() {
        heading.classList.remove('gallery-heading-motion--scroll-linked');
        heading.classList.add('gallery-heading-motion--static');
        ['--gh-opacity', '--gh-top-y', '--gh-bottom-y',
            '--gh-description-y', '--gh-blur', '--gh-scale']
            .forEach((name) => heading.style.removeProperty(name));
        section.style.removeProperty('--gallery-handoff-progress');
    }

    function render() {
        frame = 0;
        if (destroyed || document.hidden) return;
        if (!desktop.matches) {
            clearDesktopState();
            return;
        }

        const viewportHeight = window.innerHeight || 1;
        const sectionTop = section.getBoundingClientRect().top;
        const depthTop = depth
            ? depth.getBoundingClientRect().top
            : section.getBoundingClientRect().bottom;

        // Gallery mulai hidup di tengah bridge warna, bukan setelah Values selesai total.
        const handoff = smoothstep(clamp(
            (viewportHeight * 1.44 - sectionTop) / (viewportHeight * 1.08),
        ));
        const enter = smoothstep(clamp(
            (viewportHeight * 1.34 - sectionTop) / (viewportHeight * 0.88),
        ));
        const exit = smoothstep(clamp(
            (viewportHeight * 0.22 - depthTop) / (viewportHeight * 0.50),
        ));
        const opacity = smoothstep(enter) * (1 - exit);

        heading.style.setProperty('--gh-opacity', opacity.toFixed(4));
        heading.style.setProperty(
            '--gh-top-y', `${((1 - enter) * 44 - exit * 24).toFixed(2)}%`,
        );
        heading.style.setProperty(
            '--gh-bottom-y', `${((1 - enter) * -44 - exit * 28).toFixed(2)}%`,
        );
        heading.style.setProperty(
            '--gh-description-y', `${((1 - enter) * 32 - exit * 18).toFixed(2)}%`,
        );
        heading.style.setProperty(
            '--gh-blur', `${((1 - enter) * 20 + exit * 6).toFixed(2)}px`,
        );
        heading.style.setProperty(
            '--gh-scale', (0.975 + enter * 0.025 - exit * 0.008).toFixed(4),
        );
        section.style.setProperty('--gallery-handoff-progress', handoff.toFixed(4));
    }

    function requestRender() {
        if (!frame && !destroyed) frame = window.requestAnimationFrame(render);
    }

    function onVisibility() {
        if (document.hidden && frame) {
            window.cancelAnimationFrame(frame);
            frame = 0;
            return;
        }
        requestRender();
    }

    function destroy(event) {
        if (event?.persisted) {
            if (frame) window.cancelAnimationFrame(frame);
            frame = 0;
            return;
        }
        destroyed = true;
        if (frame) window.cancelAnimationFrame(frame);
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', requestRender);
        window.removeEventListener('pageshow', requestRender);
        window.removeEventListener('pagehide', destroy);
        document.removeEventListener('visibilitychange', onVisibility);
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender, { passive: true });
    window.addEventListener('pageshow', requestRender);
    window.addEventListener('pagehide', destroy);
    document.addEventListener('visibilitychange', onVisibility);
    requestRender();
}
