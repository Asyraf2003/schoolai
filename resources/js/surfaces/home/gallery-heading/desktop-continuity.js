import {
    LAYER_DURATION,
    MASK_START,
    buildMaskState,
    clamp,
    paintBlindTimeline,
    readValuesExitProgress,
    smoothstep,
} from './desktop-mask.js';

const RELEASE_EPSILON = 1;

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

    function setStageActive(active) {
        section.classList.toggle('is-values-gallery-handoff', active);
        if (!maskState?.host) return;
        maskState.host.style.visibility = active ? 'visible' : 'hidden';
        maskState.host.style.opacity = active ? '1' : '0';
    }

    function clearDesktopState() {
        heading.classList.remove('gallery-heading-motion--scroll-linked');
        heading.classList.add('gallery-heading-motion--static');
        ['--gh-handoff-opacity', '--gh-media-opacity', '--gh-media-blur']
            .forEach((name) => heading.style.removeProperty(name));
        section.style.removeProperty('--gallery-handoff-progress');
        setStageActive(false);
    }

    function render() {
        frame = 0;
        if (destroyed || document.hidden) return;
        if (!desktop.matches) {
            clearDesktopState();
            return;
        }

        const sectionTop = section.getBoundingClientRect().top;
        const rawExitProgress = readValuesExitProgress(valuesWorld);

        /*
         * Jika halaman dibuka langsung di/dekat Gallery, Values controller bisa
         * belum menulis variable exit. Posisi Gallery yang sudah lewat viewport
         * berarti handoff secara semantik selesai.
         */
        const exitProgress = rawExitProgress <= 0 && sectionTop <= RELEASE_EPSILON
            ? 1
            : rawExitProgress;
        const maskProgress = clamp(
            (exitProgress - MASK_START) / (1 - MASK_START),
        );

        /*
         * Clock visual 100% berasal dari card-exit Values. sectionTop hanya
         * dipakai sebagai release guard supaya fixed Gallery tidak dilepas satu
         * frame terlalu cepat sebelum posisi normalnya benar-benar mencapai top.
         */
        const stageActive = exitProgress > 0.0001
            && (exitProgress < 0.9999 || sectionTop > RELEASE_EPSILON);
        setStageActive(stageActive);

        const masterTime = maskProgress * LAYER_DURATION;
        if (Math.abs(masterTime - previousMasterTime) > 0.0005) {
            paintBlindTimeline(maskState?.blinds, masterTime);
            previousMasterTime = masterTime;
        }

        /*
         * Heading adalah bagian Gallery scene 2. Ia muncul menjelang blinds
         * selesai pada koordinat final pojok atas. Sesudah release, media clock
         * Three.js mengambil alih blur/fade menuju media kedua.
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
        setStageActive(false);
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
