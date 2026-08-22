const SVG_NS = 'http://www.w3.org/2000/svg';
const BLIND_COUNT = 30;
const STAGGER_SPAN = 0.34;

function clamp(value) {
    return Math.max(0, Math.min(1, value));
}

function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

function easeOutCubic(value) {
    const progress = clamp(value);
    return 1 - ((1 - progress) ** 3);
}

function createBlindSet(group, vbHeight) {
    if (!group) return [];
    group.replaceChildren();

    const rowHeight = vbHeight / BLIND_COUNT;
    const blinds = [];
    let currentY = 0;

    for (let index = 0; index < BLIND_COUNT; index += 1) {
        const centerY = vbHeight - (currentY + rowHeight / 2);
        const top = document.createElementNS(SVG_NS, 'rect');
        const bottom = document.createElementNS(SVG_NS, 'rect');

        [top, bottom].forEach((rect) => {
            rect.setAttribute('x', '0');
            rect.setAttribute('width', '100');
            rect.setAttribute('height', '0');
            rect.setAttribute('fill', 'white');
            rect.setAttribute('shape-rendering', 'crispEdges');
        });

        top.setAttribute('y', centerY.toFixed(4));
        bottom.setAttribute('y', centerY.toFixed(4));
        group.append(top, bottom);

        blinds.push({
            top,
            bottom,
            centerY,
            halfHeight: rowHeight / 2,
        });
        currentY += rowHeight;
    }

    return blinds;
}

function configureMaskLayer(svg, hostWidth, hostHeight) {
    if (!svg || hostWidth <= 0 || hostHeight <= 0) return [];

    const vbWidth = 100;
    const vbHeight = (hostHeight / hostWidth) * vbWidth;
    svg.setAttribute('viewBox', `0 0 ${vbWidth} ${vbHeight}`);

    svg.querySelectorAll('[data-gallery-mask-base], [data-gallery-mask-fill]')
        .forEach((rect) => {
            rect.setAttribute('width', vbWidth.toFixed(4));
            rect.setAttribute('height', vbHeight.toFixed(4));
        });

    return createBlindSet(
        svg.querySelector('[data-gallery-mask-blinds]'),
        vbHeight,
    );
}

function buildMaskState(section) {
    const host = section.querySelector('[data-gallery-mask-handoff]');
    if (!host) return null;

    const rect = host.getBoundingClientRect();
    const light = configureMaskLayer(
        host.querySelector('[data-gallery-mask-layer="light"]'),
        rect.width,
        rect.height,
    );
    const cream = configureMaskLayer(
        host.querySelector('[data-gallery-mask-layer="cream"]'),
        rect.width,
        rect.height,
    );

    return { host, light, cream };
}

function paintBlindSet(blinds, progress) {
    if (!blinds?.length) return;

    const expanded = clamp(progress) * (1 + STAGGER_SPAN);
    const denominator = Math.max(1, blinds.length - 1);

    blinds.forEach((blind, index) => {
        const delay = (index / denominator) * STAGGER_SPAN;
        const local = easeOutCubic(expanded - delay);
        const opening = blind.halfHeight * local;
        const height = opening > 0 ? opening + 0.01 : 0;

        blind.top.setAttribute('y', (blind.centerY - opening).toFixed(4));
        blind.top.setAttribute('height', height.toFixed(4));
        blind.bottom.setAttribute('y', blind.centerY.toFixed(4));
        blind.bottom.setAttribute('height', height.toFixed(4));
    });
}

export function initialiseDesktopContinuity(heading, section, desktop) {
    let frame = 0;
    let destroyed = false;
    let maskState = buildMaskState(section);
    let previousLight = -1;
    let previousCream = -1;

    heading.classList.add('gallery-heading-motion--scroll-linked');
    heading.style.setProperty('--gh-media-blur', '0px');

    function rebuildMasks() {
        maskState = buildMaskState(section);
        previousLight = -1;
        previousCream = -1;
    }

    function clearDesktopState() {
        heading.classList.remove('gallery-heading-motion--scroll-linked');
        heading.classList.add('gallery-heading-motion--static');
        ['--gh-opacity', '--gh-media-blur']
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

        /*
         * Gallery dimulai 60svh lebih awal. Saat top section bergerak dari
         * 100% viewport ke 40%, progress tepat 0 -> 1. Ini membuat stage mask
         * selesai persis ketika corridor 60svh habis dan depth Gallery datang.
         */
        const rawHandoff = clamp(
            (viewportHeight - sectionTop) / (viewportHeight * 0.60),
        );
        const handoff = smoothstep(rawHandoff);
        const lightProgress = smoothstep(clamp(handoff / 0.66));
        const creamProgress = smoothstep(clamp((handoff - 0.38) / 0.62));

        if (Math.abs(lightProgress - previousLight) > 0.0005) {
            paintBlindSet(maskState?.light, lightProgress);
            previousLight = lightProgress;
        }
        if (Math.abs(creamProgress - previousCream) > 0.0005) {
            paintBlindSet(maskState?.cream, creamProgress);
            previousCream = creamProgress;
        }

        /* Heading baru hadir setelah mask hampir selesai. Blur berikutnya bukan
           milik handoff; depth engine akan menghapusnya dari image 1 -> 2. */
        const headingOpacity = smoothstep(clamp((creamProgress - 0.58) / 0.32));
        heading.style.setProperty('--gh-opacity', headingOpacity.toFixed(4));
        section.style.setProperty('--gallery-handoff-progress', handoff.toFixed(4));
    }

    function requestRender() {
        if (!frame && !destroyed) frame = window.requestAnimationFrame(render);
    }

    function onResize() {
        rebuildMasks();
        requestRender();
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
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', requestRender);
        window.removeEventListener('pagehide', destroy);
        document.removeEventListener('visibilitychange', onVisibility);
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', requestRender);
    window.addEventListener('pagehide', destroy);
    document.addEventListener('visibilitychange', onVisibility);
    requestRender();
}
