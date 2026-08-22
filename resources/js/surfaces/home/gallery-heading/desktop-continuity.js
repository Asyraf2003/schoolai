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
        ['--gh-opacity', '--gh-title-y', '--gh-description-y',
            '--gh-blur', '--gh-scale', '--gh-x']
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
        const headingTop = heading.getBoundingClientRect().top;
        const depthTop = depth
            ? depth.getBoundingClientRect().top
            : section.getBoundingClientRect().bottom;
        const rtl = document.documentElement.dir === 'rtl';

        // Heading lahir di dalam color bridge. Ketika bagian atas heading mulai
        // menyentuh area atas viewport, seluruh blok bergerak lateral sambil
        // section Gallery tetap melanjutkan perjalanan vertikalnya.
        const handoff = smoothstep(clamp(
            (viewportHeight * 1.04 - sectionTop) / (viewportHeight * 0.86),
        ));
        const enter = smoothstep(clamp(
            (viewportHeight * 0.96 - sectionTop) / (viewportHeight * 0.62),
        ));
        const sideExit = smoothstep(clamp(
            (viewportHeight * 0.17 - headingTop) / (viewportHeight * 0.38),
        ));
        const depthExit = smoothstep(clamp(
            (viewportHeight * 0.12 - depthTop) / (viewportHeight * 0.44),
        ));
        const exit = Math.max(sideExit, depthExit);
        const opacity = smoothstep(enter) * (1 - exit);
        const direction = rtl ? 1 : -1;

        heading.style.setProperty('--gh-opacity', opacity.toFixed(4));
        heading.style.setProperty(
            '--gh-title-y', `${((1 - enter) * 30).toFixed(2)}%`,
        );
        heading.style.setProperty(
            '--gh-description-y', `${((1 - enter) * 24).toFixed(2)}%`,
        );
        heading.style.setProperty(
            '--gh-blur', `${((1 - enter) * 14 + exit * 3).toFixed(2)}px`,
        );
        heading.style.setProperty(
            '--gh-scale', (0.985 + enter * 0.015).toFixed(4),
        );
        heading.style.setProperty(
            '--gh-x', `${(direction * exit * 112).toFixed(2)}vw`,
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
