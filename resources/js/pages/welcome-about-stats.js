(function () {
    'use strict';

    var ROOT_SELECTOR = '[data-about-stats-story]';
    var TRACK_SELECTOR = '[data-about-stats-track]';
    var STAT_SELECTOR = '[data-about-stats-item]';
    var DESKTOP_QUERY = '(min-width: 961px)';
    var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

    var STAT_DESCRIPTIONS = {
        id: [
            'Ruang belajar yang terus bertumbuh bersama anak, keluarga, dan komunitas sekolah.',
            'Pencapaian yang lahir dari proses belajar bermakna, konsisten, dan berani mencoba.',
            'Waktu belajar yang diisi dengan eksplorasi, refleksi, kolaborasi, dan pengalaman nyata.',
            'Program yang dirancang untuk menguatkan iman, ilmu, karakter, kreativitas, dan kemandirian.'
        ],
        en: [
            'A learning community that keeps growing together with children, families, and the school community.',
            'Achievements shaped by meaningful learning, consistency, curiosity, and the courage to try.',
            'Learning time filled with exploration, reflection, collaboration, and real-world experiences.',
            'Programs designed to strengthen faith, knowledge, character, creativity, and independence.'
        ],
        ar: [
            'بيئة تعليمية تنمو باستمرار مع الأطفال والأسر ومجتمع المدرسة.',
            'إنجازات تنطلق من تعلم هادف واستمرارية وفضول وشجاعة في التجربة.',
            'ساعات تعلم مليئة بالاستكشاف والتأمل والتعاون والخبرات الواقعية.',
            'برامج صممت لتعزيز الإيمان والعلم والشخصية والإبداع والاستقلالية.'
        ]
    };

    function clamp(value, minimum, maximum) {
        return Math.min(Math.max(value, minimum), maximum);
    }

    function lerp(from, to, progress) {
        return from + (to - from) * progress;
    }

    function smootherstep(edgeStart, edgeEnd, value) {
        if (edgeStart === edgeEnd) return value < edgeStart ? 0 : 1;

        var progress = clamp(
            (value - edgeStart) / (edgeEnd - edgeStart),
            0,
            1
        );

        return progress * progress * progress * (
            progress * (progress * 6 - 15) + 10
        );
    }

    function easeOutCubic(value) {
        var inverse = 1 - clamp(value, 0, 1);
        return 1 - inverse * inverse * inverse;
    }

    function setNumberProperty(element, property, value, precision) {
        if (!element) return;

        var digits = typeof precision === 'number' ? precision : 4;
        element.style.setProperty(property, Number(value).toFixed(digits));
    }

    function setPixelProperty(element, property, value) {
        if (!element) return;
        element.style.setProperty(property, Number(value).toFixed(2) + 'px');
    }

    function localeKey() {
        var language = (document.documentElement.lang || '').toLowerCase();

        if (language.indexOf('ar') === 0) return 'ar';
        if (language.indexOf('en') === 0) return 'en';
        return 'id';
    }

    function isRtlDocument() {
        return (
            (document.documentElement.dir || '').toLowerCase() === 'rtl' ||
            localeKey() === 'ar'
        );
    }

    function ensureStatDescription(statElement, index) {
        if (!statElement) return;

        var callout = statElement.querySelector(
            '.about-stats-story__stat-callout'
        );
        if (!callout) return;

        if (callout.querySelector('.about-stats-story__stat-description')) {
            return;
        }

        var descriptions = STAT_DESCRIPTIONS[localeKey()] || STAT_DESCRIPTIONS.id;
        var description = document.createElement('p');
        var line = callout.querySelector('.about-stats-story__stat-line');

        description.className = 'about-stats-story__stat-description';
        description.textContent = descriptions[index % descriptions.length];

        if (line) {
            callout.insertBefore(description, line);
        } else {
            callout.appendChild(description);
        }
    }

    function addMediaListener(mediaQuery, listener) {
        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', listener);
            return;
        }

        if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(listener);
        }
    }

    function removeMediaListener(mediaQuery, listener) {
        if (typeof mediaQuery.removeEventListener === 'function') {
            mediaQuery.removeEventListener('change', listener);
            return;
        }

        if (typeof mediaQuery.removeListener === 'function') {
            mediaQuery.removeListener(listener);
        }
    }

    function initializeStory(root) {
        if (root.getAttribute('data-about-stats-initialized') === 'true') {
            return;
        }

        var track = root.querySelector(TRACK_SELECTOR);
        var sticky = root.querySelector('.about-stats-story__sticky') || root;
        var statElements = Array.prototype.slice.call(
            root.querySelectorAll(STAT_SELECTOR)
        );
        var progressDots = Array.prototype.slice.call(
            root.querySelectorAll('[data-about-stats-progress-dot]')
        );
        var desktopMedia = window.matchMedia(DESKTOP_QUERY);
        var reducedMotionMedia = window.matchMedia(REDUCED_MOTION_QUERY);
        var state = {
            enhanced: false,
            resizeTimer: null,
            progressFrame: null,
            lastFrameTime: 0,
            targetProgress: 0,
            renderedProgress: 0,
            pointerFrame: null,
            pointerListening: false,
            pointerX: 0,
            pointerY: 0,
            activeIndex: -1,
            direction: isRtlDocument() ? -1 : 1,
            mobileObserver: null
        };

        if (!track) return;

        root.setAttribute('data-about-stats-initialized', 'true');
        statElements.forEach(ensureStatDescription);

        function updateActiveIndex(index) {
            if (state.activeIndex === index) return;

            state.activeIndex = index;
            root.setAttribute(
                'data-active-stat',
                index >= 0 ? String(index) : ''
            );

            progressDots.forEach(function (dot, dotIndex) {
                dot.classList.toggle('is-active', dotIndex === index);
            });
        }

        function setStoryHeight() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );
            var screenCount = Math.max(
                4.9,
                2 + statElements.length * 0.82
            );

            root.style.setProperty(
                '--story-scroll-height',
                Math.round(viewportHeight * screenCount) + 'px'
            );
        }

        function readScrollProgress() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                1
            );
            var rect = track.getBoundingClientRect();
            var scrollRange = Math.max(track.offsetHeight - viewportHeight, 1);

            return clamp(-rect.top / scrollRange, 0, 1);
        }

        function renderScene(progress) {
            if (!state.enhanced) return;

            var viewportWidth = Math.max(
                window.innerWidth || 0,
                document.documentElement.clientWidth || 0,
                1
            );
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                1
            );
            var statStart = 0.105;
            var statEnd = 0.94;
            var statCount = Math.max(statElements.length, 1);
            var segment = (statEnd - statStart) / statCount;
            var introEntry = smootherstep(0, 0.075, progress);
            var outro = smootherstep(0.972, 1, progress);
            var activeIndex = -1;
            var aboutSide = 0;

            if (progress >= statStart && statElements.length) {
                activeIndex = clamp(
                    Math.floor((progress - statStart) / segment),
                    0,
                    statElements.length - 1
                );

                var segmentStart = statStart + activeIndex * segment;
                var localProgress = clamp(
                    (progress - segmentStart) / segment,
                    0,
                    1
                );
                var currentSide =
                    (activeIndex % 2 === 0 ? 1 : -1) * state.direction;
                var previousSide = activeIndex === 0
                    ? 0
                    : ((activeIndex - 1) % 2 === 0 ? 1 : -1) *
                        state.direction;
                var sideBlend = smootherstep(0, 0.55, localProgress);

                aboutSide = lerp(previousSide, currentSide, sideBlend);
            }

            var aboutMovePresence = smootherstep(
                statStart - 0.02,
                statStart + segment * 0.34,
                progress
            );
            var aboutX =
                aboutSide * viewportWidth * 0.235 * aboutMovePresence;
            var aboutY = lerp(
                viewportHeight * 0.24,
                0,
                easeOutCubic(introEntry)
            );
            var aboutScale =
                lerp(0.95, 1, introEntry) - aboutMovePresence * 0.18;
            var aboutOpacity = introEntry * (1 - outro);
            var storyStatProgress = clamp(
                (progress - statStart) / (statEnd - statStart),
                0,
                1
            );

            setPixelProperty(root, '--about-x', aboutX);
            setPixelProperty(root, '--about-y', aboutY);
            setNumberProperty(root, '--about-scale', Math.max(aboutScale, 0.8));
            setNumberProperty(root, '--about-opacity', aboutOpacity);
            setNumberProperty(root, '--story-progress', storyStatProgress);
            setNumberProperty(
                root,
                '--progress-opacity',
                smootherstep(statStart - 0.025, statStart + 0.05, progress) *
                    (1 - outro)
            );

            statElements.forEach(function (statElement, index) {
                var start = statStart + index * segment;
                var local = (progress - start) / segment;
                var enter = smootherstep(-0.2, 0.28, local);
                var exit = smootherstep(0.72, 1.12, local);
                var presence = enter * (1 - exit) * (1 - outro);
                var side =
                    (index % 2 === 0 ? -1 : 1) * state.direction;
                var x = side * viewportWidth * 0.185;
                var y =
                    (1 - enter) * viewportHeight * 0.26 -
                    exit * viewportHeight * 0.28;
                var scale =
                    lerp(0.92, 1, easeOutCubic(enter)) - exit * 0.045;
                var blur =
                    lerp(14, 0, easeOutCubic(enter)) + exit * 9;

                setNumberProperty(statElement, '--stat-opacity', presence);
                setPixelProperty(statElement, '--stat-x', x);
                setPixelProperty(statElement, '--stat-y', y);
                setNumberProperty(statElement, '--stat-scale', scale);
                setPixelProperty(statElement, '--stat-blur', blur);
            });

            updateActiveIndex(activeIndex);
        }

        function stopProgressLoop() {
            if (state.progressFrame !== null) {
                window.cancelAnimationFrame(state.progressFrame);
                state.progressFrame = null;
            }

            state.lastFrameTime = 0;
        }

        function progressLoop(timestamp) {
            if (!state.enhanced) {
                stopProgressLoop();
                return;
            }

            var deltaFrames = state.lastFrameTime
                ? clamp((timestamp - state.lastFrameTime) / 16.667, 0.5, 4)
                : 1;
            var smoothing = 1 - Math.pow(0.925, deltaFrames);
            var difference = state.targetProgress - state.renderedProgress;

            state.lastFrameTime = timestamp;
            state.renderedProgress += difference * smoothing;

            if (Math.abs(difference) < 0.00008) {
                state.renderedProgress = state.targetProgress;
            }

            renderScene(state.renderedProgress);

            if (state.renderedProgress !== state.targetProgress) {
                state.progressFrame = window.requestAnimationFrame(progressLoop);
                return;
            }

            state.progressFrame = null;
            state.lastFrameTime = 0;
        }

        function startProgressLoop() {
            if (!state.enhanced || state.progressFrame !== null) return;
            state.progressFrame = window.requestAnimationFrame(progressLoop);
        }

        function syncScrollTarget(immediate) {
            if (!state.enhanced) return;

            state.targetProgress = readScrollProgress();

            if (immediate) {
                stopProgressLoop();
                state.renderedProgress = state.targetProgress;
                renderScene(state.renderedProgress);
                return;
            }

            startProgressLoop();
        }

        function renderPointer() {
            state.pointerFrame = null;
            if (!state.enhanced) return;

            var viewportWidth = Math.max(window.innerWidth || 1, 1);
            var viewportHeight = Math.max(window.innerHeight || 1, 1);
            var normalizedX = clamp(
                (state.pointerX / viewportWidth - 0.5) * 2,
                -1,
                1
            );
            var normalizedY = clamp(
                (state.pointerY / viewportHeight - 0.5) * 2,
                -1,
                1
            );

            setPixelProperty(root, '--ambient-x', normalizedX * 11);
            setPixelProperty(root, '--ambient-y', normalizedY * 8);
            setPixelProperty(root, '--ambient-x-reverse', normalizedX * -8);
            setPixelProperty(root, '--ambient-y-reverse', normalizedY * -6);
        }

        function requestPointerRender(event) {
            if (!state.enhanced || event.pointerType === 'touch') return;

            state.pointerX = event.clientX;
            state.pointerY = event.clientY;

            if (state.pointerFrame !== null) return;
            state.pointerFrame = window.requestAnimationFrame(renderPointer);
        }

        function resetPointer() {
            if (state.pointerFrame !== null) {
                window.cancelAnimationFrame(state.pointerFrame);
                state.pointerFrame = null;
            }

            root.style.setProperty('--ambient-x', '0px');
            root.style.setProperty('--ambient-y', '0px');
            root.style.setProperty('--ambient-x-reverse', '0px');
            root.style.setProperty('--ambient-y-reverse', '0px');
        }

        function listenForPointer() {
            if (state.pointerListening) return;

            sticky.addEventListener('pointermove', requestPointerRender, {
                passive: true
            });
            sticky.addEventListener('pointerleave', resetPointer, {
                passive: true
            });
            state.pointerListening = true;
        }

        function stopListeningForPointer() {
            if (!state.pointerListening) return;

            sticky.removeEventListener('pointermove', requestPointerRender);
            sticky.removeEventListener('pointerleave', resetPointer);
            state.pointerListening = false;
            resetPointer();
        }

        function animateMobileStat(statElement) {
            if (statElement.getAttribute('data-mobile-animated') === 'true') {
                return;
            }

            statElement.setAttribute('data-mobile-animated', 'true');
            statElement.classList.add('is-mobile-visible');
        }

        function setupLinearMode(mode) {
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
                    threshold: 0.16,
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
            });
        }

        function teardownMode() {
            window.removeEventListener('scroll', onDesktopScroll);
            window.removeEventListener('resize', onDesktopResize);
            window.clearTimeout(state.resizeTimer);
            stopProgressLoop();
            stopListeningForPointer();

            if (state.mobileObserver) {
                state.mobileObserver.disconnect();
                state.mobileObserver = null;
            }

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
                setStoryHeight();
                syncScrollTarget(true);
            }, 120);
        }

        function setupMode() {
            teardownMode();

            if (desktopMedia.matches && !reducedMotionMedia.matches) {
                state.enhanced = true;
                state.direction = isRtlDocument() ? -1 : 1;
                root.setAttribute('data-enhanced', 'true');
                root.setAttribute('data-mode', 'desktop');

                /*
                 * Runtime proof confirmed that overflow:hidden on the story root
                 * prevents the sticky scene from pinning. overflow:clip preserves
                 * visual clipping without creating the sticky containment bug.
                 */
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

    function initialize() {
        document.querySelectorAll(ROOT_SELECTOR).forEach(initializeStory);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, {
            once: true
        });
    } else {
        initialize();
    }
})();