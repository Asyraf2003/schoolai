import { addMediaListener, isRtlDocument, removeMediaListener } from './core.js';
import { buildTimeline } from './timeline.js';

export function installStoryMode(context, layout, progress) {
    var root = context.root;
    var statElements = context.statElements;
    var desktopMedia = context.desktopMedia;
    var reducedMotionMedia = context.reducedMotionMedia;
    var state = context.state;
    var updateActiveIndex = layout.updateActiveIndex;
    var setStoryHeight = layout.setStoryHeight;
    var stopProgressLoop = progress.stopProgressLoop;
    var syncScrollTarget = progress.syncScrollTarget;
    var listenForPointer = progress.listenForPointer;
    var stopListeningForPointer = progress.stopListeningForPointer;

        function animateMobileStat(statElement) {
            if (statElement.getAttribute('data-mobile-animated') === 'true') {
                return;
            }

            statElement.setAttribute('data-mobile-animated', 'true');
            statElement.classList.add('is-mobile-visible');
        }

        function disconnectMobileObserver() {
            if (!state.mobileObserver) return;
            state.mobileObserver.disconnect();
            state.mobileObserver = null;
        }

        function setupLinearMode(mode) {
            disconnectMobileObserver();
            root.setAttribute('data-enhanced', 'false');
            root.setAttribute('data-mode', mode);
            root.style.removeProperty('--story-scroll-height');
            root.style.removeProperty('overflow');
            updateActiveIndex(-1);

            statElements.forEach(function (statElement) {
                statElement.removeAttribute('data-mobile-animated');
                statElement.classList.remove('is-mobile-visible');
                statElement.style.removeProperty('--stat-opacity');
                statElement.style.removeProperty('--stat-x');
                statElement.style.removeProperty('--stat-y');
                statElement.style.removeProperty('--stat-scale');
                statElement.style.removeProperty('--stat-blur');
                statElement.style.removeProperty('--stat-settle');
            });

            if (
                mode === 'static' ||
                typeof IntersectionObserver !== 'function'
            ) {
                statElements.forEach(animateMobileStat);
                return;
            }

            state.mobileObserver = new IntersectionObserver(
                function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;

                        animateMobileStat(entry.target);
                        observer.unobserve(entry.target);
                    });
                },
                {
                    threshold: 0.15,
                    rootMargin: '0px 0px -4% 0px'
                }
            );

            statElements.forEach(function (statElement) {
                state.mobileObserver.observe(statElement);
            });
        }

        function clearDesktopProperties() {
            [
                '--about-x',
                '--about-y',
                '--about-scale',
                '--about-opacity',
                '--story-progress',
                '--progress-opacity'
            ].forEach(function (property) {
                root.style.removeProperty(property);
            });

            statElements.forEach(function (statElement) {
                statElement.style.removeProperty('--stat-opacity');
                statElement.style.removeProperty('--stat-x');
                statElement.style.removeProperty('--stat-y');
                statElement.style.removeProperty('--stat-scale');
                statElement.style.removeProperty('--stat-blur');
                statElement.style.removeProperty('--stat-settle');
            });
        }

        function teardownMode() {
            window.removeEventListener('scroll', onDesktopScroll);
            window.removeEventListener('resize', onDesktopResize);
            window.clearTimeout(state.resizeTimer);
            stopProgressLoop();
            stopListeningForPointer();
            disconnectMobileObserver();

            state.enhanced = false;
            root.style.removeProperty('overflow');
            clearDesktopProperties();
        }

        function onDesktopScroll() {
            syncScrollTarget(false);
        }

        function onDesktopResize() {
            window.clearTimeout(state.resizeTimer);
            state.resizeTimer = window.setTimeout(function () {
                state.direction = isRtlDocument() ? -1 : 1;
                context.timeline = buildTimeline(statElements.length);
                setStoryHeight();
                syncScrollTarget(true);
            }, 140);
        }

        function setupMode() {
            teardownMode();

            if (desktopMedia.matches && !reducedMotionMedia.matches) {
                state.enhanced = true;
                state.direction = isRtlDocument() ? -1 : 1;
                context.timeline = buildTimeline(statElements.length);
                root.setAttribute('data-enhanced', 'true');
                root.setAttribute('data-mode', 'desktop');
                root.style.overflow = 'clip';

                setStoryHeight();
                listenForPointer();

                window.addEventListener('scroll', onDesktopScroll, {
                    passive: true
                });
                window.addEventListener('resize', onDesktopResize, {
                    passive: true
                });

                syncScrollTarget(true);
                return;
            }

            setupLinearMode(
                reducedMotionMedia.matches ? 'static' : 'mobile'
            );
        }

        setupMode();
        addMediaListener(desktopMedia, setupMode);
        addMediaListener(reducedMotionMedia, setupMode);

        window.addEventListener(
            'pagehide',
            function cleanup() {
                teardownMode();
                removeMediaListener(desktopMedia, setupMode);
                removeMediaListener(reducedMotionMedia, setupMode);
            },
            { once: true }
        );
}
