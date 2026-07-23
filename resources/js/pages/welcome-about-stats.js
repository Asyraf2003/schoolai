(function () {
    'use strict';

    var ROOT_SELECTOR = '[data-about-stats-story]';
    var TRACK_SELECTOR = '[data-about-stats-track]';
    var STAT_SELECTOR = '[data-about-stats-item]';
    var NUMBER_SELECTOR = '[data-about-stats-number]';
    var LABEL_SELECTOR = '[data-about-stats-label]';
    var VIDEO_SELECTOR = '[data-about-stats-video]';
    var DESKTOP_QUERY = '(min-width: 961px)';
    var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

    var DIGIT_FAMILIES = {
        latin: '0123456789',
        arabicIndic: '٠١٢٣٤٥٦٧٨٩',
        easternArabic: '۰۱۲۳۴۵۶۷۸۹'
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

    function easeOutQuint(value) {
        var inverse = 1 - clamp(value, 0, 1);
        return 1 - inverse * inverse * inverse * inverse * inverse;
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

    function setDegreeProperty(element, property, value) {
        if (!element) return;
        element.style.setProperty(property, Number(value).toFixed(3) + 'deg');
    }

    function digitDetails(character) {
        var familyNames = Object.keys(DIGIT_FAMILIES);

        for (var index = 0; index < familyNames.length; index += 1) {
            var family = familyNames[index];
            var value = DIGIT_FAMILIES[family].indexOf(character);

            if (value !== -1) {
                return {
                    family: family,
                    value: value
                };
            }
        }

        return null;
    }

    function glyphForDigit(value, family) {
        var glyphs = DIGIT_FAMILIES[family] || DIGIT_FAMILIES.latin;
        return glyphs.charAt(value % 10);
    }

    function graphemesForText(text) {
        if (
            typeof Intl !== 'undefined' &&
            typeof Intl.Segmenter === 'function'
        ) {
            try {
                var segmenter = new Intl.Segmenter(
                    document.documentElement.lang || undefined,
                    { granularity: 'grapheme' }
                );

                return Array.from(segmenter.segment(text), function (entry) {
                    return entry.segment;
                });
            } catch (error) {
                // Array.from is still Unicode aware for this fallback.
            }
        }

        return Array.from(text);
    }

    function createDigitReel(character, digitIndex, totalDigits) {
        var details = digitDetails(character);
        var reel = document.createElement('span');
        var track = document.createElement('span');
        var turns = 2 + ((totalDigits - digitIndex) % 3);
        var maximumStep = turns * 10 + details.value;
        var fragment = document.createDocumentFragment();

        reel.className = 'about-stats-story__digit';
        reel.setAttribute('aria-hidden', 'true');

        track.className = 'about-stats-story__digit-track';
        track.style.setProperty('--reel-offset', '0em');

        for (var step = 0; step <= maximumStep; step += 1) {
            var cell = document.createElement('span');
            cell.className = 'about-stats-story__digit-cell';
            cell.textContent = glyphForDigit(step, details.family);
            fragment.appendChild(cell);
        }

        track.appendChild(fragment);
        reel.appendChild(track);

        return {
            element: reel,
            track: track,
            maximumStep: maximumStep
        };
    }

    function buildOdometer(numberElement) {
        if (!numberElement) return [];

        var rawValue = (
            numberElement.getAttribute('data-stat-value') ||
            numberElement.textContent ||
            ''
        ).trim();
        var characters = Array.from(rawValue);
        var totalDigits = characters.reduce(function (total, character) {
            return total + (digitDetails(character) ? 1 : 0);
        }, 0);
        var accessibleValue = document.createElement('span');
        var reels = [];
        var digitIndex = 0;
        var fragment = document.createDocumentFragment();

        if (!totalDigits) {
            numberElement.setAttribute('data-odometer-ready', 'false');
            return reels;
        }

        characters.forEach(function (character) {
            if (digitDetails(character)) {
                var reel = createDigitReel(
                    character,
                    digitIndex,
                    totalDigits
                );

                reels.push(reel);
                fragment.appendChild(reel.element);
                digitIndex += 1;
                return;
            }

            var token = document.createElement('span');
            token.className = 'about-stats-story__number-token';
            token.setAttribute('aria-hidden', 'true');
            token.textContent = character;
            fragment.appendChild(token);
        });

        accessibleValue.className = 'sr-only';
        accessibleValue.textContent = rawValue;

        numberElement.textContent = '';
        numberElement.removeAttribute('aria-label');
        numberElement.setAttribute('data-odometer-ready', 'true');
        numberElement.appendChild(accessibleValue);
        numberElement.appendChild(fragment);

        return reels;
    }

    function letterizeLabel(labelElement, isRtl) {
        if (!labelElement) {
            return {
                letters: [],
                count: 1
            };
        }

        var text = (labelElement.textContent || '').trim();

        /*
         * Arabic must remain shaped. Splitting Arabic into graphemes detaches
         * joined letters, so RTL labels animate word-by-word instead.
         */
        var units = text.split(/(\s+)/);
        var visibleCount = units.reduce(function (total, unit) {
            if (/^\s+$/.test(unit)) return total;

            return total + (
                isRtl
                    ? 1
                    : graphemesForText(unit).length
            );
        }, 0);
        var visibleIndex = 0;
        var accessibleLabel = document.createElement('span');
        var letters = [];
        var fragment = document.createDocumentFragment();

        accessibleLabel.className = 'sr-only';
        accessibleLabel.textContent = text;

        labelElement.textContent = '';
        labelElement.removeAttribute('aria-label');
        labelElement.appendChild(accessibleLabel);

        units.forEach(function (unit) {
            var isSpace = /^\s+$/.test(unit);

            if (isSpace) {
                var space = document.createElement('span');
                space.className =
                    'about-stats-story__letter ' +
                    'about-stats-story__letter--space';
                space.setAttribute('aria-hidden', 'true');
                space.textContent = unit;
                fragment.appendChild(space);
                return;
            }

            var word = document.createElement('span');
            var wordUnits = isRtl
                ? [unit]
                : graphemesForText(unit);

            word.className = 'about-stats-story__label-word';
            word.setAttribute('aria-hidden', 'true');

            wordUnits.forEach(function (wordUnit) {
                var letter = document.createElement('span');

                letter.className = 'about-stats-story__letter';
                letter.setAttribute('aria-hidden', 'true');
                letter.textContent = wordUnit;
                letter.style.setProperty(
                    '--letter-order',
                    String(visibleIndex)
                );
                letter.style.setProperty('--letter-progress', '0');
                visibleIndex += 1;
                letters.push(letter);
                word.appendChild(letter);
            });

            fragment.appendChild(word);
        });

        labelElement.appendChild(fragment);

        return {
            letters: letters,
            count: Math.max(visibleCount, 1)
        };
    }

    function setOdometerProgress(reels, progress) {
        var baseProgress = clamp(progress, 0, 1);

        reels.forEach(function (reel, index) {
            var delay = Math.min(index * 0.045, 0.18);
            var localProgress = clamp(
                (baseProgress - delay) / Math.max(1 - delay, 0.01),
                0,
                1
            );
            var step = reel.maximumStep * easeOutQuint(localProgress);

            reel.track.style.setProperty(
                '--reel-offset',
                (-step).toFixed(4) + 'em'
            );
        });
    }

    function setLetterProgress(letterData, progress) {
        if (!letterData || !letterData.letters.length) return;

        var baseProgress = clamp(progress, 0, 1);
        var count = letterData.count;

        letterData.letters.forEach(function (letter, index) {
            var delay = (index / count) * 0.44;
            var localProgress = clamp(
                (baseProgress - delay) / Math.max(1 - delay, 0.01),
                0,
                1
            );

            var progress = easeOutCubic(localProgress);
            var outlineProgress = clamp(
                1 - Math.abs(progress - 0.44) * 2.25,
                0,
                1
            );

            letter.style.setProperty(
                '--letter-progress',
                progress.toFixed(4)
            );
            letter.style.setProperty(
                '--letter-outline-alpha',
                (outlineProgress * 0.68).toFixed(4)
            );
            letter.style.setProperty(
                '--letter-opacity',
                lerp(0.22, 1, progress).toFixed(4)
            );
            letter.style.setProperty(
                '--letter-y',
                lerp(18, 0, progress).toFixed(2) + 'px'
            );
            letter.style.setProperty(
                '--letter-glow-size',
                (progress * 14).toFixed(2) + 'px'
            );
        });
    }

    function setStatContentProgress(statData, progress) {
        setOdometerProgress(statData.reels, progress);
        setLetterProgress(statData.letterData, progress);
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
        var sticky =
            root.querySelector('.about-stats-story__sticky') ||
            root;
        var video = root.querySelector(VIDEO_SELECTOR);
        var statElements = Array.prototype.slice.call(
            root.querySelectorAll(STAT_SELECTOR)
        );
        var progressDots = Array.prototype.slice.call(
            root.querySelectorAll('[data-about-stats-progress-dot]')
        );
        var desktopMedia = window.matchMedia(DESKTOP_QUERY);
        var reducedMotionMedia = window.matchMedia(
            REDUCED_MOTION_QUERY
        );
        var documentLanguage = document.documentElement.lang || '';
        var isRtl =
            document.documentElement.dir === 'rtl' ||
            /^ar(?:-|$)/i.test(documentLanguage);
        var state = {
            enhanced: false,
            ticking: false,
            resizeTimer: null,
            pointerFrame: null,
            pointerClientX: 0,
            pointerClientY: 0,
            pointerListening: false,
            mobileObserver: null,
            videoObserver: null,
            videoVisible: false,
            videoHydrated: false,
            activeIndex: -1,
            stats: []
        };

        if (!track) return;

        root.setAttribute('data-about-stats-initialized', 'true');

        statElements.forEach(function (statElement) {
            var numberElement = statElement.querySelector(NUMBER_SELECTOR);
            var labelElement = statElement.querySelector(LABEL_SELECTOR);

            state.stats.push({
                element: statElement,
                reels: buildOdometer(numberElement),
                letterData: letterizeLabel(labelElement, isRtl),
                mobileAnimated: false
            });
        });

        function hydrateVideo() {
            if (!video || state.videoHydrated) return;

            var hydrated = false;

            video.querySelectorAll('source[data-src]').forEach(
                function (source) {
                    var sourceUrl = source.getAttribute('data-src');
                    if (!sourceUrl) return;

                    source.src = sourceUrl;
                    source.removeAttribute('data-src');
                    hydrated = true;
                }
            );

            if (hydrated) {
                state.videoHydrated = true;
                video.setAttribute('data-hydrated', 'true');
                video.load();
            }
        }

        function syncVideo() {
            if (!video) return;

            var shouldPlay =
                state.videoVisible &&
                !document.hidden &&
                !reducedMotionMedia.matches;

            if (!shouldPlay) {
                video.pause();
                return;
            }

            hydrateVideo();

            var playAttempt = video.play();
            if (
                playAttempt &&
                typeof playAttempt.catch === 'function'
            ) {
                playAttempt.catch(function () {
                    root.classList.add('has-media-playback-fallback');
                });
            }
        }

        function setupVideoObserver() {
            if (!video || typeof IntersectionObserver !== 'function') {
                return;
            }

            state.videoObserver = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.target !== root) return;

                        state.videoVisible =
                            entry.isIntersecting &&
                            entry.intersectionRatio > 0.08;

                        if (state.videoVisible) hydrateVideo();
                        syncVideo();
                    });
                },
                {
                    threshold: [0, 0.08, 0.24],
                    rootMargin: '80% 0px 80% 0px'
                }
            );

            state.videoObserver.observe(root);
        }

        function updateActiveIndex(activeIndex) {
            if (activeIndex === state.activeIndex) return;

            state.activeIndex = activeIndex;
            root.setAttribute(
                'data-active-stat',
                activeIndex >= 0 ? String(activeIndex) : ''
            );

            progressDots.forEach(function (dot, index) {
                dot.classList.toggle('is-active', index === activeIndex);
            });
        }

        function setStoryHeight() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );
            var screenCount = Math.max(
                3.65,
                2.9 + state.stats.length * 0.19
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
            var scrollRange = Math.max(
                track.offsetHeight - viewportHeight,
                1
            );
            var progress = clamp(-rect.top / scrollRange, 0, 1);
            var approach = 1 - smoothstep(
                viewportHeight * 0.08,
                viewportHeight * 0.72,
                rect.top
            );
            var entry = Math.max(
                approach,
                smoothstep(0, 0.09, progress)
            );
            var entryEase = easeOutCubic(entry);
            var shrink = easeInOutCubic(
                smoothstep(0.08, 0.25, progress)
            );
            var outro = smoothstep(0.94, 1, progress);
            var entryScale = lerp(0.79, 1.025, entryEase);
            var mediaScale = lerp(entryScale, 0.57, shrink);
            var mediaY =
                lerp(viewportHeight * 0.31, 0, entryEase) +
                lerp(0, viewportHeight * 0.008, shrink) -
                viewportHeight * 0.16 * outro;
            var mediaOpacity =
                smoothstep(0.04, 0.38, entry) *
                (1 - outro * 0.74);
            var introPresence =
                smoothstep(0.2, 0.7, entry) *
                (1 - smoothstep(0.12, 0.24, progress)) *
                (1 - outro);
            var introY =
                lerp(viewportHeight * 0.19, 0, entryEase) -
                shrink * viewportHeight * 0.17;
            var introScale =
                lerp(0.92, 1, entryEase) -
                shrink * 0.055;
            var ambientOpacity =
                lerp(0.08, 0.88, smoothstep(0.2, 0.46, progress)) *
                (1 - outro * 0.35);
            var statStart = 0.27;
            var statInterval = state.stats.length > 1
                ? Math.min(0.145, 0.5 / (state.stats.length - 1))
                : 0;
            var activeIndex = -1;
            var activeSide = 0;
            var storyStatProgress = clamp(
                (progress - statStart) /
                    Math.max(
                        0.58,
                        statInterval * Math.max(state.stats.length - 1, 1) +
                            0.12
                    ),
                0,
                1
            );

            if (progress >= statStart - 0.018 && state.stats.length) {
                activeIndex = clamp(
                    Math.floor(
                        (progress - statStart + statInterval * 0.28) /
                            Math.max(statInterval, 0.001)
                    ),
                    0,
                    state.stats.length - 1
                );
                activeSide = activeIndex % 2 === 0 ? -1 : 1;
                if (isRtl) activeSide *= -1;
            }

            var mediaShift =
                activeIndex >= 0
                    ? -activeSide * viewportWidth * 0.012
                    : 0;

            setNumberProperty(root, '--story-progress', storyStatProgress);
            setNumberProperty(root, '--ambient-opacity', ambientOpacity);
            setNumberProperty(root, '--media-scale', mediaScale);
            setPixelProperty(root, '--media-x', mediaShift);
            setPixelProperty(root, '--media-y', mediaY);
            setNumberProperty(root, '--media-opacity', mediaOpacity);
            setNumberProperty(root, '--frame-progress', shrink);
            setPixelProperty(root, '--frame-radius', shrink * 32);
            setPixelProperty(root, '--frame-border-dark', shrink * 11);
            setPixelProperty(root, '--frame-border-light', shrink * 12);
            setPixelProperty(root, '--frame-shadow-y', shrink * 38);
            setPixelProperty(root, '--frame-shadow-blur', shrink * 84);
            setNumberProperty(
                root,
                '--frame-shadow-alpha',
                shrink * 0.25
            );
            setNumberProperty(
                root,
                '--media-image-scale',
                lerp(1.055, 1.02, shrink)
            );
            setNumberProperty(
                root,
                '--media-shade',
                lerp(0.64, 0.09, shrink)
            );
            setNumberProperty(
                root,
                '--media-shade-soft',
                lerp(0.45, 0.06, shrink)
            );
            setNumberProperty(
                root,
                '--media-shade-mid',
                lerp(0.15, 0.02, shrink)
            );
            setNumberProperty(
                root,
                '--media-glint-opacity',
                shrink * 0.7
            );
            setNumberProperty(root, '--tv-opacity', shrink);
            setNumberProperty(root, '--intro-opacity', introPresence);
            setPixelProperty(root, '--intro-y', introY);
            setNumberProperty(root, '--intro-scale', introScale);
            setNumberProperty(
                root,
                '--hint-opacity',
                smoothstep(0.22, 0.56, entry) *
                    (1 - smoothstep(0.075, 0.16, progress))
            );
            setNumberProperty(
                root,
                '--progress-opacity',
                smoothstep(0.22, 0.31, progress) *
                    (1 - smoothstep(0.93, 0.985, progress))
            );
            setDegreeProperty(
                root,
                '--orbit-rotate',
                lerp(-5, 7, progress)
            );

            state.stats.forEach(function (statData, index) {
                var start = statStart + index * statInterval;
                var enter = smoothstep(start - 0.025, start + 0.075, progress);
                var nextStart = index < state.stats.length - 1
                    ? statStart + (index + 1) * statInterval
                    : 2;
                var passed = smoothstep(
                    nextStart + 0.015,
                    nextStart + 0.09,
                    progress
                );
                var side = index % 2 === 0 ? -1 : 1;
                if (isRtl) side *= -1;

                var opacity =
                    enter *
                    lerp(1, 0.54, passed) *
                    (1 - outro);
                var translateY =
                    lerp(viewportHeight * 0.09, 0, easeOutCubic(enter)) -
                    outro * viewportHeight * 0.05;
                var translateX =
                    side *
                    lerp(viewportWidth * 0.025, 0, easeOutCubic(enter));
                var scale =
                    lerp(0.78, 1, easeOutCubic(enter)) -
                    passed * 0.055;
                var contentProgress = smoothstep(
                    start - 0.015,
                    start + 0.08,
                    progress
                );
                var labelProgress = smoothstep(
                    start + 0.005,
                    start + 0.095,
                    progress
                );

                setNumberProperty(
                    statData.element,
                    '--stat-opacity',
                    opacity
                );
                setPixelProperty(
                    statData.element,
                    '--stat-x',
                    translateX
                );
                setPixelProperty(
                    statData.element,
                    '--stat-y',
                    translateY
                );
                setNumberProperty(
                    statData.element,
                    '--stat-scale',
                    Math.max(scale, 0.7)
                );
                setNumberProperty(
                    statData.element,
                    '--stat-line-scale',
                    smoothstep(start + 0.015, start + 0.09, progress)
                );
                setNumberProperty(
                    statData.element,
                    '--stat-glow',
                    enter * (1 - passed * 0.72)
                );
                setPixelProperty(
                    statData.element,
                    '--stat-glow-size',
                    enter * (1 - passed * 0.72) * 32
                );

                setOdometerProgress(statData.reels, contentProgress);
                setLetterProgress(statData.letterData, labelProgress);
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
                (state.pointerClientX / viewportWidth - 0.5) * 2,
                -1,
                1
            );
            var normalizedY = clamp(
                (state.pointerClientY / viewportHeight - 0.5) * 2,
                -1,
                1
            );

            setPixelProperty(root, '--pointer-x', normalizedX * 13);
            setPixelProperty(root, '--pointer-y', normalizedY * 10);
            setPixelProperty(
                root,
                '--pointer-x-reverse',
                normalizedX * -9
            );
            setPixelProperty(
                root,
                '--pointer-y-reverse',
                normalizedY * -7
            );
            setPixelProperty(
                root,
                '--pointer-x-soft',
                normalizedX * 5
            );
            setPixelProperty(
                root,
                '--pointer-y-soft',
                normalizedY * -4
            );
            setPixelProperty(
                root,
                '--pointer-x-media',
                normalizedX * 1.2
            );
            setPixelProperty(
                root,
                '--pointer-y-media',
                normalizedY * 0.9
            );
            setDegreeProperty(root, '--media-tilt-x', normalizedY * -0.7);
            setDegreeProperty(root, '--media-tilt-y', normalizedX * 0.9);
        }

        function requestPointerRender(event) {
            if (!state.enhanced || event.pointerType === 'touch') return;

            state.pointerClientX = event.clientX;
            state.pointerClientY = event.clientY;

            if (state.pointerFrame !== null) return;

            state.pointerFrame = window.requestAnimationFrame(renderPointer);
        }

        function resetPointer() {
            if (state.pointerFrame !== null) {
                window.cancelAnimationFrame(state.pointerFrame);
                state.pointerFrame = null;
            }

            root.style.setProperty('--pointer-x', '0px');
            root.style.setProperty('--pointer-y', '0px');
            root.style.setProperty('--pointer-x-reverse', '0px');
            root.style.setProperty('--pointer-y-reverse', '0px');
            root.style.setProperty('--pointer-x-soft', '0px');
            root.style.setProperty('--pointer-y-soft', '0px');
            root.style.setProperty('--pointer-x-media', '0px');
            root.style.setProperty('--pointer-y-media', '0px');
            root.style.setProperty('--media-tilt-x', '0deg');
            root.style.setProperty('--media-tilt-y', '0deg');
        }

        function listenForPointer() {
            if (state.pointerListening) return;

            sticky.addEventListener(
                'pointermove',
                requestPointerRender,
                { passive: true }
            );
            sticky.addEventListener(
                'pointerleave',
                resetPointer,
                { passive: true }
            );
            state.pointerListening = true;
        }

        function stopListeningForPointer() {
            if (!state.pointerListening) return;

            sticky.removeEventListener(
                'pointermove',
                requestPointerRender
            );
            sticky.removeEventListener('pointerleave', resetPointer);
            state.pointerListening = false;
            resetPointer();
        }

        function animateMobileStat(statData) {
            if (statData.mobileAnimated) return;
            statData.mobileAnimated = true;

            statData.element.classList.add('is-mobile-visible');

            if (reducedMotionMedia.matches) {
                setStatContentProgress(statData, 1);
                return;
            }

            var startTime = null;
            var duration = 880;

            function frame(timestamp) {
                if (!startTime) startTime = timestamp;

                var progress = clamp(
                    (timestamp - startTime) / duration,
                    0,
                    1
                );

                setStatContentProgress(statData, progress);

                if (progress < 1) {
                    window.requestAnimationFrame(frame);
                }
            }

            window.requestAnimationFrame(frame);
        }

        function setupLinearMode(mode) {
            root.setAttribute('data-enhanced', 'false');
            root.setAttribute('data-mode', mode);
            root.style.removeProperty('--story-scroll-height');
            updateActiveIndex(-1);

            state.stats.forEach(function (statData) {
                statData.mobileAnimated = false;
                statData.element.classList.remove('is-mobile-visible');
                statData.element.style.removeProperty('--stat-opacity');
                statData.element.style.removeProperty('--stat-x');
                statData.element.style.removeProperty('--stat-y');
                statData.element.style.removeProperty('--stat-scale');
                statData.element.style.removeProperty('--stat-line-scale');
                statData.element.style.removeProperty('--stat-glow');
                statData.element.style.removeProperty('--stat-glow-size');
                setStatContentProgress(statData, 0);
            });

            if (
                mode === 'static' ||
                typeof IntersectionObserver !== 'function'
            ) {
                state.stats.forEach(animateMobileStat);
                return;
            }

            state.mobileObserver = new IntersectionObserver(
                function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;

                        var statData = state.stats.find(
                            function (candidate) {
                                return candidate.element === entry.target;
                            }
                        );

                        if (!statData) return;

                        animateMobileStat(statData);
                        observer.unobserve(entry.target);
                    });
                },
                {
                    threshold: 0.16,
                    rootMargin: '0px 0px -4% 0px'
                }
            );

            state.stats.forEach(function (statData) {
                state.mobileObserver.observe(statData.element);
            });
        }

        function clearDesktopProperties() {
            [
                '--story-progress',
                '--ambient-opacity',
                '--media-scale',
                '--media-x',
                '--media-y',
                '--media-opacity',
                '--frame-progress',
                '--frame-radius',
                '--frame-border-dark',
                '--frame-border-light',
                '--frame-shadow-y',
                '--frame-shadow-blur',
                '--frame-shadow-alpha',
                '--media-image-scale',
                '--media-shade',
                '--media-shade-soft',
                '--media-shade-mid',
                '--media-glint-opacity',
                '--tv-opacity',
                '--intro-opacity',
                '--intro-y',
                '--intro-scale',
                '--hint-opacity',
                '--progress-opacity',
                '--orbit-rotate'
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
            clearDesktopProperties();
        }

        function onDesktopResize() {
            window.clearTimeout(state.resizeTimer);
            state.resizeTimer = window.setTimeout(function () {
                setStoryHeight();
                requestDesktopRender();
            }, 120);
        }

        function setupMode() {
            teardownMode();

            var shouldEnhance =
                desktopMedia.matches &&
                !reducedMotionMedia.matches;

            if (shouldEnhance) {
                state.enhanced = true;
                root.setAttribute('data-enhanced', 'true');
                root.setAttribute('data-mode', 'desktop');

                state.stats.forEach(function (statData) {
                    statData.mobileAnimated = false;
                    statData.element.classList.remove('is-mobile-visible');
                    setStatContentProgress(statData, 0);
                });

                setStoryHeight();
                listenForPointer();
                window.addEventListener(
                    'scroll',
                    requestDesktopRender,
                    { passive: true }
                );
                window.addEventListener(
                    'resize',
                    onDesktopResize,
                    { passive: true }
                );
                requestDesktopRender();
                syncVideo();
                return;
            }

            setupLinearMode(
                reducedMotionMedia.matches ? 'static' : 'mobile'
            );
            syncVideo();
        }

        function onVisibilityChange() {
            syncVideo();
        }

        setupVideoObserver();
        setupMode();

        addMediaListener(desktopMedia, setupMode);
        addMediaListener(reducedMotionMedia, setupMode);
        document.addEventListener(
            'visibilitychange',
            onVisibilityChange
        );

        window.addEventListener(
            'pagehide',
            function cleanup() {
                teardownMode();

                if (state.videoObserver) {
                    state.videoObserver.disconnect();
                }

                if (video) video.pause();

                removeMediaListener(desktopMedia, setupMode);
                removeMediaListener(reducedMotionMedia, setupMode);
                document.removeEventListener(
                    'visibilitychange',
                    onVisibilityChange
                );
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
