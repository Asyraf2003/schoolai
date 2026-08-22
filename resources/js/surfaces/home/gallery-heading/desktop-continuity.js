const SVG_NS = 'http://www.w3.org/2000/svg';
const BLIND_COUNT = 30;
const BLIND_DURATION = 0.5;
const BLIND_STAGGER = 0.02;
const LAYER_DURATION = BLIND_DURATION + ((BLIND_COUNT - 1) * BLIND_STAGGER);
const MASK_START = 0.06;

function clamp(value) {
    return Math.max(0, Math.min(1, value));
}

function power3Out(value) {
    const progress = clamp(value);
    return 1 - ((1 - progress) ** 3);
}

function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

function readValuesExitProgress(world) {
    if (!world) return 0;
    const raw = getComputedStyle(world)
        .getPropertyValue('--values-gallery-exit-progress')
        .trim();
    const value = Number.parseFloat(raw);
    return Number.isFinite(value) ? clamp(value) : 0;
}

function createBlindSet(group, vbHeight) {
    if (!group) return [];
    group.replaceChildren();

    const rowHeight = vbHeight / BLIND_COUNT;
    const blinds = [];
    let currentY = 0;

    for (let index = 0; index < BLIND_COUNT; index += 1) {
        /* Repo source menyusun blind dari bawah ke atas. */
        const centerY = vbHeight - (currentY + rowHeight / 2);
        const top = document.createElementNS(SVG_NS, 'rect');
        const bottom = document.createElementNS(SVG_NS, 'rect');

        [top, bottom].forEach((rect) => {
            rect.setAttribute('x', '0');
            rect.setAttribute('width', '100');
            rect.setAttribute('height', '0');
            /*
             * Source memakai white-on-black untuk membuka image berikutnya.
             * Di SchoolAI Gallery DOM sudah berada di bawah scene Values, jadi
             * mask dibalik: rect hitam menghapus cover blue dan memperlihatkan
             * Gallery asli di bawahnya.
             */
            rect.setAttribute('fill', 'black');
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

    const width = window.innerWidth || host.getBoundingClientRect().width;
    const height = window.innerHeight || host.getBoundingClientRect().height;
    const blinds = configureMaskLayer(
        host.querySelector('[data-gallery-mask-layer="values"]'),
        width,
        height,
    );

    return { host, blinds };
}

function paintBlindTimeline(blinds, masterTime) {
    if (!blinds?.length) return;

    blinds.forEach((blind, index) => {
        const blindStart = index * BLIND_STAGGER;
        const local = power3Out(
            (masterTime - blindStart) / BLIND_DURATION,
        );
        const opening = blind.halfHeight * local;
        const height = opening > 0 ? opening + 0.01 : 0;

        blind.top.setAttribute('y', (blind.centerY - opening).toFixed(4));
        blind.top.setAttribute('height', height.toFixed(4));
        blind.bottom.setAttribute('y', blind.centerY.toFixed(4));
        blind.bottom.setAttribute('height', height.toFixed(4));
    });
}

export function initialiseDesktopContinuity(heading, section, desktop) {
    const valuesWorld = document.querySelector('[data-program-values-world]');
    let frame = 0;
    let destroyed = false;
    let maskState = buildMaskState(section);
    let previousMasterTime = -1;

    heading.classList.add('gallery-heading-motion--scroll-linked');
    heading.style.setProperty('--gh-handoff-opacity', '0');
    heading.style.setProperty('--gh-media-opacity', '1');
    heading.style.setProperty('--gh-media-blur', '0px');

    function rebuildMasks() {
        maskState = buildMaskState(section);
        previousMasterTime = -1;
    }

    function resetMaskHost() {
        if (!maskState?.host) return;
        maskState.host.style.visibility = 'hidden';
        maskState.host.style.opacity = '0';
        maskState.host.style.transform = 'translate3d(0, 0, 0)';
    }

    function clearDesktopState() {
        heading.classList.remove('gallery-heading-motion--scroll-linked');
        heading.classList.add('gallery-heading-motion--static');
        ['--gh-handoff-opacity', '--gh-media-opacity', '--gh-media-blur']
            .forEach((name) => heading.style.removeProperty(name));
        section.style.removeProperty('--gallery-handoff-progress');
        resetMaskHost();
    }

    function render() {
        frame = 0;
        if (destroyed || document.hidden) return;
        if (!desktop.matches) {
            clearDesktopState();
            return;
        }

        const exitProgress = readValuesExitProgress(valuesWorld);
        const maskProgress = clamp(
            (exitProgress - MASK_START) / (1 - MASK_START),
        );
        const sectionTop = section.getBoundingClientRect().top;
        const stageActive = exitProgress > 0.0001 && exitProgress < 0.9999;

        if (maskState?.host) {
            if (stageActive) {
                /* Pin full-viewport layer ke layar selama kartu sedang exit. */
                maskState.host.style.visibility = 'visible';
                maskState.host.style.opacity = '1';
                maskState.host.style.transform = `translate3d(0, ${(-sectionTop).toFixed(2)}px, 0)`;
            } else {
                resetMaskHost();
            }
        }

        const masterTime = maskProgress * LAYER_DURATION;
        if (Math.abs(masterTime - previousMasterTime) > 0.0005) {
            paintBlindTimeline(maskState?.blinds, masterTime);
            previousMasterTime = masterTime;
        }

        /*
         * Heading adalah bagian scene Gallery, jadi baru hadir ketika blinds
         * hampir selesai. Sesudah itu clock heading diserahkan ke media Gallery.
         */
        const headingHandoffOpacity = smoothstep(
            clamp((maskProgress - 0.78) / 0.22),
        );
        heading.style.setProperty(
            '--gh-handoff-opacity',
            headingHandoffOpacity.toFixed(4),
        );
        section.style.setProperty(
            '--gallery-handoff-progress',
            exitProgress.toFixed(4),
        );
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
        resetMaskHost();
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
