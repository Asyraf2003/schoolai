(function () {
    'use strict';

    var ROOT_SELECTOR = '[data-about-stats-story]';
    var STAT_SELECTOR = '[data-about-stats-item]';
    var NUMBER_SELECTOR = '[data-about-stats-number]';
    var LABEL_SELECTOR = '[data-about-stats-label]';
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

    function setNumberProperty(element, property, value, precision) {
        if (!element) return;

        var digits = typeof precision === 'number' ? precision : 4;
        element.style.setProperty(property, Number(value).toFixed(digits));
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
                // Array.from remains a safe Unicode-aware fallback.
            }
        }

        return Array.from(text);
    }

    function createDigitReel(character, digitIndex, totalDigits) {
        var details = digitDetails(character);
        var reel = document.createElement('span');
        var track = document.createElement('span');
        var turns = 2 + ((totalDigits - digitIndex) % 4);
        var maximumStep = turns * 10 + details.value;
        var fragment = document.createDocumentFragment();

        reel.className = 'about-stats-story__digit';
        reel.setAttribute('aria-hidden', 'true');

        track.className = 'about-stats-story__digit-track';
        track.style.setProperty('--reel-step', '0');

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
        if (!numberElement) {
            return [];
        }

        var rawValue = (
            numberElement.getAttribute('data-stat-value') ||
            numberElement.textContent ||
            ''
        ).trim();
        var characters = Array.from(rawValue);
        var totalDigits = characters.reduce(function (total, character) {
            return total + (digitDetails(character) ? 1 : 0);
        }, 0);
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

        numberElement.textContent = '';
        numberElement.setAttribute('aria-label', rawValue);
        numberElement.setAttribute('data-odometer-ready', 'true');
        numberElement.appendChild(fragment);

        return reels;
    }

    function letterizeLabel(labelElement, isRtl) {
        if (!labelElement) {
            return [];
        }

        var text = (labelElement.textContent || '').trim();
        var units = isRtl
            ? text.split(/(\s+)/)
            : graphemesForText(text);
        var visibleCount = units.reduce(function (total, unit) {
            return total + (/\s/.test(unit) ? 0 : 1);
        }, 0);
        var visibleIndex = 0;
        var letters = [];
        var fragment = document.createDocumentFragment();

        labelElement.textContent = '';
        labelElement.setAttribute('aria-label', text);

        units.forEach(function (unit) {
            var letter = document.createElement('span');
            var isSpace = /^\s+$/.test(unit);

            letter.className = 'about-stats-story__letter';
            letter.setAttribute('aria-hidden', 'true');
            letter.textContent = unit;

            if (isSpace) {
                letter.classList.add('about-stats-story__letter--space');
            } else {
                letter.style.setProperty(
                    '--letter-order',
                    String(visibleIndex)
                );
                visibleIndex += 1;
            }

            letter.style.setProperty('--letter-progress', '0');
            fragment.appendChild(letter);

            if (!isSpace) {
                letters.push(letter);
            }
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
            var delay = Math.min(index * 0.045, 0.2);
            var localProgress = clamp(
                (baseProgress - delay) / Math.max(1 - delay, 0.01),
                0,
                1
            );
            var step = reel.maximumStep * easeOutQuint(localProgress);

            reel.track.style.setProperty('--reel-step', step.toFixed(4));
        });
    }

    function setLetterProgress(letterData, progress) {
        if (!letterData || !letterData.letters.length) return;

        var baseProgress = clamp(progress, 0, 1);
        var count = letterData.count;

        letterData.letters.forEach(function (letter, index) {
            var delay = (index / count) * 0.48;
            var localProgress = clamp(
                (baseProgress - delay) / Math.max(1 - delay, 0.01),
                0,
                1
            );

            letter.style.setProperty(
                '--letter-progress',
                easeOutCubic(localProgress).toFixed(4)
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

    function initializeStory(root) {
        if (root.getAttribute('data-about-stats-initialized') === 'true') {
            return;
        }

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
        var isRtl = document.documentElement.dir === 'rtl';
        var state = {
            enhanced: false,
            ticking: false,
            mobileObserver: null,
            resizeTimer: null,
            activeIndex: -1,
            stats: []
        };

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

        function updateProgressDots(activeIndex) {
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

        function resetVisualProperties() {
            root.style.removeProperty('--progress-opacity');
            root.style.removeProperty('--hint-opacity');
            root.style.removeProperty('--about-opacity');
            root.style.removeProperty('--about-word-opacity');
            root.style.removeProperty('--about-word-progress');
            root.style.removeProperty('--about-x');
            root.style.removeProperty('--about-y');
            root.style.removeProperty('--about-scale');
            root.style.removeProperty('--about-rotate');
            root.style.removeProperty('--about-z-rotate');

            state.stats.forEach(function (statData) {
                statData.element.style.removeProperty('--stat-opacity');
                statData.element.style.removeProperty('--stat-x');
                statData.element.style.removeProperty('--stat-y');
                statData.element.style.removeProperty('--stat-scale');
                statData.element.style.removeProperty('--stat-aura-opacity');
            });
        }

        function measureDesktopHeight() {
            if (!state.enhanced) return;

            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );
            var screenCount = Math.max(state.stats.length + 2.45, 4.8);
            var storyHeight = Math.round(viewportHeight * screenCount);

            root.style.setProperty(
                '--story-scroll-height',
                storyHeight + 'px'
            );
        }

        function renderDesktopStory() {
            state.ticking = false;

            if (!state.enhanced || !state.stats.length) return;

            var viewportHeight = Math.max(window.innerHeight || 1, 1);
            var viewportWidth = Math.max(window.innerWidth || 1, 1);
            var rect = root.getBoundingClientRect();
            var scrollRange = Math.max(root.offsetHeight - viewportHeight, 1);
            var progress = clamp(-rect.top / scrollRange, 0, 1);
            var introStart = 0;
            var introEnd = 0.2;
            var storyEnd = 0.88;
            var segment = (storyEnd - introEnd) / state.stats.length;
            var introProgress = smoothstep(
                introStart,
                introEnd * 0.72,
                progress
            );
            var sideProgress = smoothstep(
                introEnd * 0.62,
                introEnd + segment * 0.24,
                progress
            );
            var outroProgress = smoothstep(storyEnd, 0.995, progress);
            var approximateIndex = clamp(
                Math.floor(
                    (progress - introEnd + segment * 0.18) / segment
                ),
                0,
                state.stats.length - 1
            );
            var activeIndex = progress < introEnd * 0.88
                ? -1
                : approximateIndex;
            var firstSide = -1;
            var targetSide = activeIndex >= 0
                ? (activeIndex % 2 === 0 ? -1 : 1)
                : firstSide;
            var previousSide = activeIndex > 0
                ? ((activeIndex - 1) % 2 === 0 ? -1 : 1)
                : 0;
            var activeLocal = activeIndex >= 0
                ? (progress - (introEnd + activeIndex * segment)) / segment
                : 0;
            var sideBlend = activeIndex === 0
                ? sideProgress
                : smoothstep(-0.08, 0.3, activeLocal);
            var aboutSide = activeIndex <= 0
                ? lerp(0, targetSide, sideBlend)
                : lerp(previousSide, targetSide, sideBlend);
            var aboutHorizontal = aboutSide * viewportWidth * 0.255;
            var aboutEnterY = lerp(
                viewportHeight * 0.46,
                0,
                easeOutCubic(introProgress)
            );
            var aboutExitY = -viewportHeight * 0.55 * outroProgress;
            var aboutOpacity = smoothstep(
                0.012,
                introEnd * 0.48,
                progress
            ) * (1 - smoothstep(0.91, 1, progress));
            var aboutScale = lerp(
                0.9,
                1,
                introProgress
            ) - sideProgress * 0.31 - outroProgress * 0.05;
            var aboutRotation = aboutSide * -6.4;

            root.style.setProperty(
                '--about-x',
                aboutHorizontal.toFixed(2) + 'px'
            );
            root.style.setProperty(
                '--about-y',
                (aboutEnterY + aboutExitY).toFixed(2) + 'px'
            );
            root.style.setProperty(
                '--about-rotate',
                aboutRotation.toFixed(2) + 'deg'
            );
            root.style.setProperty(
                '--about-z-rotate',
                (aboutSide * 0.65).toFixed(2) + 'deg'
            );
            setNumberProperty(root, '--about-opacity', aboutOpacity);
            setNumberProperty(
                root,
                '--about-scale',
                Math.max(aboutScale, 0.56)
            );
            setNumberProperty(root, '--about-word-progress', introProgress);
            setNumberProperty(
                root,
                '--about-word-opacity',
                1 - outroProgress * 0.74
            );
            setNumberProperty(
                root,
                '--hint-opacity',
                smoothstep(0.04, 0.1, progress) *
                    (1 - smoothstep(0.13, introEnd, progress))
            );
            setNumberProperty(
                root,
                '--progress-opacity',
                smoothstep(introEnd * 0.83, introEnd + 0.03, progress) *
                    (1 - smoothstep(0.9, 0.97, progress))
            );

            var mostVisibleIndex = -1;
            var mostVisibleOpacity = 0;

            state.stats.forEach(function (statData, index) {
                var start = introEnd + index * segment;
                var local = (progress - start) / segment;
                var enter = smoothstep(-0.08, 0.28, local);
                var leave = 1 - smoothstep(0.78, 1.16, local);
                var opacity = enter * leave;
                var side = index % 2 === 0 ? 1 : -1;
                var targetX = side * viewportWidth * 0.255;
                var entryX = side * viewportWidth * 0.065 * (1 - enter);
                var entryY = viewportHeight * 0.29 * (1 - enter);
                var exitY = -viewportHeight * 0.3 *
                    smoothstep(0.75, 1.16, local);
                var scale = lerp(0.9, 1, enter) -
                    smoothstep(0.8, 1.16, local) * 0.055;
                var contentProgress = smoothstep(0.02, 0.52, local);
                var labelProgress = smoothstep(0.16, 0.62, local);

                statData.element.style.setProperty(
                    '--stat-x',
                    (targetX + entryX).toFixed(2) + 'px'
                );
                statData.element.style.setProperty(
                    '--stat-y',
                    (entryY + exitY).toFixed(2) + 'px'
                );
                setNumberProperty(
                    statData.element,
                    '--stat-opacity',
                    opacity
                );
                setNumberProperty(
                    statData.element,
                    '--stat-scale',
                    Math.max(scale, 0.82)
                );
                setNumberProperty(
                    statData.element,
                    '--stat-aura-opacity',
                    opacity * smoothstep(0.12, 0.5, local)
                );

                setOdometerProgress(statData.reels, contentProgress);
                setLetterProgress(statData.letterData, labelProgress);

                if (opacity > mostVisibleOpacity) {
                    mostVisibleOpacity = opacity;
                    mostVisibleIndex = index;
                }
            });

            updateProgressDots(
                mostVisibleOpacity > 0.08 ? mostVisibleIndex : activeIndex
            );
        }

        function requestDesktopRender() {
            if (!state.enhanced || state.ticking) return;

            state.ticking = true;
            window.requestAnimationFrame(renderDesktopStory);
        }

        function onDesktopResize() {
            if (!state.enhanced) return;

            window.clearTimeout(state.resizeTimer);
            state.resizeTimer = window.setTimeout(function () {
                measureDesktopHeight();
                requestDesktopRender();
            }, 120);
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
            var duration = 1250;

            function frame(timestamp) {
                if (!startTime) startTime = timestamp;

                var progress = clamp(
                    (timestamp - startTime) / duration,
                    0,
                    1
                );

                setStatContentProgress(statData, easeOutCubic(progress));

                if (progress < 1) {
                    window.requestAnimationFrame(frame);
                }
            }

            window.requestAnimationFrame(frame);
        }

        function setupLinearStory() {
            state.stats.forEach(function (statData) {
                statData.mobileAnimated = false;
                statData.element.classList.remove('is-mobile-visible');
                setStatContentProgress(statData, 0);
            });

            if (
                reducedMotionMedia.matches ||
                !('IntersectionObserver' in window)
            ) {
                state.stats.forEach(function (statData) {
                    animateMobileStat(statData);
                });
                return;
            }

            state.mobileObserver = new IntersectionObserver(
                function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;

                        var index = statElements.indexOf(entry.target);

                        if (index >= 0) {
                            animateMobileStat(state.stats[index]);
                        }

                        observer.unobserve(entry.target);
                    });
                },
                {
                    threshold: 0.28,
                    rootMargin: '0px 0px -8% 0px'
                }
            );

            statElements.forEach(function (statElement) {
                state.mobileObserver.observe(statElement);
            });
        }

        function teardownCurrentMode() {
            window.removeEventListener('scroll', requestDesktopRender);
            window.removeEventListener('resize', onDesktopResize);
            window.clearTimeout(state.resizeTimer);

            if (state.mobileObserver) {
                state.mobileObserver.disconnect();
                state.mobileObserver = null;
            }

            state.ticking = false;
            root.style.removeProperty('--story-scroll-height');
            resetVisualProperties();
            updateProgressDots(-1);
        }

        function applyMode() {
            teardownCurrentMode();

            state.enhanced =
                desktopMedia.matches &&
                !reducedMotionMedia.matches &&
                state.stats.length > 0;

            root.setAttribute(
                'data-enhanced',
                state.enhanced ? 'true' : 'false'
            );

            if (state.enhanced) {
                state.stats.forEach(function (statData) {
                    statData.element.classList.remove(
                        'is-mobile-visible'
                    );
                    setStatContentProgress(statData, 0);
                });

                measureDesktopHeight();
                window.addEventListener('scroll', requestDesktopRender, {
                    passive: true
                });
                window.addEventListener('resize', onDesktopResize, {
                    passive: true
                });
                requestDesktopRender();
                return;
            }

            setupLinearStory();
        }

        addMediaListener(desktopMedia, applyMode);
        addMediaListener(reducedMotionMedia, applyMode);
        window.addEventListener('pageshow', function () {
            if (state.enhanced) {
                measureDesktopHeight();
                requestDesktopRender();
            }
        });

        applyMode();
    }

    function boot() {
        var roots = document.querySelectorAll(ROOT_SELECTOR);

        Array.prototype.forEach.call(roots, initializeStory);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
