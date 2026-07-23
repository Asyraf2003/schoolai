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

    function smoothstep(edgeStart, edgeEnd, value) {
        if (edgeStart === edgeEnd) {
            return value < edgeStart ? 0 : 1;
        }

        var progress = clamp(
            (value - edgeStart) / (edgeEnd - edgeStart),
            0,
            1
        );

        return progress * progress * (3 - 2 * progress);
    }

    function easeOutCubic(value) {
        var inverse = 1 - clamp(value, 0, 1);
        return 1 - inverse * inverse * inverse;
    }

    function easeInOutCubic(value) {
        var progress = clamp(value, 0, 1);

        return progress < 0.5
            ? 4 * progress * progress * progress
            : 1 - Math.pow(-2 * progress + 2, 3) / 2;
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
        var explicitDirection = document.documentElement.dir;

        if (explicitDirection) {
            return explicitDirection.toLowerCase() === 'rtl';
        }

        if (window.getComputedStyle) {
            return window.getComputedStyle(document.documentElement).direction === 'rtl';
        }

        return localeKey() === 'ar';
    }

    function ensureStatDescription(statElement, index) {
        if (!statElement) return;

        var callout = statElement.querySelector(
            '.about-stats-story__stat-callout'
        );
        if (!callout) return;

        var existing = callout.querySelector(
            '.about-stats-story__stat-description'
        );
        if (existing) return;

        var descriptions = STAT_DESCRIPTIONS[localeKey()] || STAT_DESCRIPTIONS.id;
        var description = document.createElement('p');
        var line = callout.querySelector('.about-stats-story__stat-line');

        description.className = 'about-stats-story__stat-description';
        description.textContent = descriptions[index % descriptions.length];

        if (line) {
            callout.insertBefore(description, line);
            return;
        }

        callout.appendChild(description);
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
        var intro = root.querySelector('.about-stats-story__intro');
        var statElements = Array.prototype.slice.call(
            root.querySelectorAll(STAT_SELECTOR)
        );
        var progressDots = Array.prototype.slice.call(
            root.querySelectorAll('[data-about-stats-progress-dot]')
        );
        var desktopMedia = window.matchMedia(DESKTOP_QUERY);
        var reducedMotionMedia = window.matchMedia(REDUCED_MOTION_QUERY);
        var rtl = isRtlDocument();
        var layoutDirection = rtl ? -1 : 1;
        var state = {
            enhanced: false,
            ticking: false,
            resizeTimer: null,
            pointerFrame: null,
            pointerListening: false,
            pointerX: 0,
            pointerY: 0,
            activeIndex: -1,
            mobileObserver: null
        };

        if (!track) return;

        root.setAttribute('data-about-stats-initialized', 'true');
        statElements.forEach(ensureStatDescription);

        function applyPhysicalCentering(enabled) {
            var introTransform =
                'translate3d(calc(-50% + var(--about-x)), ' +
                'calc(-50% + var(--about-y)), 0) ' +
                'scale(var(--about-scale))';
            var statTransform =
                'translate3d(calc(-50% + var(--stat-x)), ' +
                'calc(-50% + var(--stat-y)), 0) ' +
                'scale(var(--stat-scale))';

            if (!enabled || !rtl) {
                if (intro) intro.style.removeProperty('transform');
                statElements.forEach(function (statElement) {
                    statElement.style.removeProperty('transform');
                });
                return;
            }

            /*
             * The scene is physically centered with left: 50%. RTL should
             * mirror motion direction, not change translateX(-50%) to +50%.
             * Keeping this inline prevents an older RTL override from pushing
             * the entire Arabic scene one full element-width off screen.
             */
            if (intro) intro.style.transform = introTransform;
            statElements.forEach(function (statElement) {
                statElement.style.transform = statTransform;
            });
        }

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
                5.2,
                3.6 + statElements.length * 0.68
            );

            root.style.setProperty(
                '--story-scroll-height',
                Math.round(viewportHeight * screenCount) + 'px'
            );
        }

        function renderDesktop() {
            state.ticking = false;
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
            var rect = track.getBoundingClientRect();
            var scrollRange = Math.max(track.offsetHeight - viewportHeight, 1);
            var progress = clamp(-rect.top / scrollRange, 0, 1);
            var introEntry = smoothstep(0.01, 0.135, progress);
            var introEntryEase = easeOutCubic(introEntry);
            var outro = smoothstep(0.94, 1, progress);
            var statStart = 0.18;
            var statEnd = 0.91;
            var statCount = Math.max(statElements.length, 1);
            var segment = (statEnd - statStart) / statCount;
            var storyStatProgress = clamp(
                (progress - statStart) / (statEnd - statStart),
                0,
                1
            );
            var activeIndex = -1;
            var aboutSide = 0;
            var aboutPresence = smoothstep(
                statStart - 0.025,
                statStart + 0.055,
                progress
            );

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
                    (activeIndex % 2 === 0 ? 1 : -1) * layoutDirection;
                var previousSide = activeIndex === 0
                    ? 0
                    : ((activeIndex - 1) % 2 === 0 ? 1 : -1) *
                        layoutDirection;
                var sideBlend = easeInOutCubic(
                    smoothstep(0, 0.28, localProgress)
                );

                aboutSide = lerp(previousSide, currentSide, sideBlend);
            }

            var aboutX = aboutSide * viewportWidth * 0.235 * aboutPresence;
            var aboutY =
                lerp(viewportHeight * 0.3, 0, introEntryEase) -
                aboutPresence * viewportHeight * 0.028 -
                outro * viewportHeight * 0.08;
            var aboutScale =
                lerp(0.93, 1, introEntryEase) -
                aboutPresence * 0.205 -
                outro * 0.04;
            var aboutOpacity = introEntry * (1 - outro * 0.82);

            setPixelProperty(root, '--about-x', aboutX);
            setPixelProperty(root, '--about-y', aboutY);
            setNumberProperty(root, '--about-scale', Math.max(aboutScale, 0.7));
            setNumberProperty(root, '--about-opacity', aboutOpacity);
            setNumberProperty(root, '--story-progress', storyStatProgress);
            setNumberProperty(
                root,
                '--progress-opacity',
                smoothstep(statStart - 0.015, statStart + 0.04, progress) *
                    (1 - outro)
            );

            statElements.forEach(function (statElement, index) {
                var start = statStart + index * segment;
                var end = start + segment;
                var enter = smoothstep(
                    start,
                    start + segment * 0.26,
                    progress
                );
                var exit = smoothstep(
                    start + segment * 0.7,
                    end,
                    progress
                );
                var presence = enter * (1 - exit) * (1 - outro);
                var side = (index % 2 === 0 ? -1 : 1) * layoutDirection;
                var entryX = side * viewportWidth * 0.5;
                var targetX = side * viewportWidth * 0.17;
                var exitX = -side * viewportWidth * 0.44;
                var x = exit > 0
                    ? lerp(targetX, exitX, easeInOutCubic(exit))
                    : lerp(entryX, targetX, easeOutCubic(enter));
                var y =
                    lerp(viewportHeight * 0.23, 0, easeOutCubic(enter)) -
                    exit * viewportHeight * 0.09;
                var scale =
                    lerp(0.82, 1, easeOutCubic(enter)) -
                    exit * 0.08;
                var blur =
                    lerp(12, 0, easeOutCubic(enter)) +
                    exit * 8;

                setNumberProperty(statElement, '--stat-opacity', presence);
                setPixelProperty(statElement, '--stat-x', x);
                setPixelProperty(statElement, '--stat-y', y);
                setNumberProperty(statElement, '--stat-scale', scale);
                setPixelProperty(statElement, '--stat-blur', blur);
            });

            updateActiveIndex(activeIndex);
        }

        function requestDesktopRender() {
            if (!state.enhanced || state.ticking) return;

            state.ticking = true;
            window.requestAnimationFrame(renderDesktop);
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

            setPixelProperty(root, '--ambient-x', normalizedX * 12);
            setPixelProperty(root, '--ambient-y', normalizedY * 9);
            setPixelProperty(root, '--ambient-x-reverse', normalizedX * -9);
            setPixelProperty(root, '--ambient-y-reverse', normalizedY * -7);
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
            updateActiveIndex(-1);
            applyPhysicalCentering(false);

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
        }

        function teardownMode() {
            window.removeEventListener('scroll', requestDesktopRender);
            window.removeEventListener('resize', onDesktopResize);
            window.clearTimeout(state.resizeTimer);

            stopListeningForPointer();

            if (state.mobileObserver) {
                state.mobileObserver.disconnect();
                state.mobileObserver = null;
            }

            state.enhanced = false;
            state.ticking = false;
            applyPhysicalCentering(false);
            clearDesktopProperties();
        }

        function onDesktopResize() {
            window.clearTimeout(state.resizeTimer);
            state.resizeTimer = window.setTimeout(function () {
                rtl = isRtlDocument();
                layoutDirection = rtl ? -1 : 1;
                applyPhysicalCentering(state.enhanced);
                setStoryHeight();
                requestDesktopRender();
            }, 120);
        }

        function setupMode() {
            teardownMode();

            rtl = isRtlDocument();
            layoutDirection = rtl ? -1 : 1;

            if (desktopMedia.matches && !reducedMotionMedia.matches) {
                state.enhanced = true;
                root.setAttribute('data-enhanced', 'true');
                root.setAttribute('data-mode', 'desktop');
                applyPhysicalCentering(true);
                setStoryHeight();
                listenForPointer();

                window.addEventListener('scroll', requestDesktopRender, {
                    passive: true
                });
                window.addEventListener('resize', onDesktopResize, {
                    passive: true
                });

                requestDesktopRender();
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
