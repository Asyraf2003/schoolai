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
        var accessibleLabel = document.createElement('span');
        var letters = [];
        var fragment = document.createDocumentFragment();

        accessibleLabel.className = 'sr-only';
        accessibleLabel.textContent = text;

        labelElement.textContent = '';
        labelElement.removeAttribute('aria-label');
        labelElement.appendChild(accessibleLabel);

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
        var pointerTarget =
            root.querySelector('.about-stats-story__sticky') ||
            root;
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
            mobileObserver: null,
            resizeTimer: null,
            pointerFrame: null,
            pointerClientX: 0,
            pointerClientY: 0,
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

            var activeStat = activeIndex >= 0
                ? state.stats[activeIndex]
                : null;
            var sceneAccent = activeStat
                ? activeStat.element.style.getPropertyValue('--stat-accent')
                : '';

            root.style.setProperty(
                '--scene-accent',
                sceneAccent || '#f2a713'
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
            root.style.removeProperty('--about-word-clip');
            root.style.removeProperty('--about-word-y');
            root.style.removeProperty('--about-line-scale');
            root.style.removeProperty('--about-art-opacity');
            root.style.removeProperty('--about-art-scale');
            root.style.removeProperty('--about-detail-opacity');
            root.style.removeProperty('--about-copy-y');
            root.style.removeProperty('--about-x');
            root.style.removeProperty('--about-y');
            root.style.removeProperty('--about-scale');
            root.style.removeProperty('--about-rotate');
            root.style.removeProperty('--about-z-rotate');
            root.style.removeProperty('--pointer-x');
            root.style.removeProperty('--pointer-y');
            root.style.removeProperty('--pointer-x-reverse');
            root.style.removeProperty('--pointer-y-reverse');

            state.stats.forEach(function (statData) {
                statData.element.style.removeProperty('--stat-opacity');
                statData.element.style.removeProperty('--stat-x');
                statData.element.style.removeProperty('--stat-y');
                statData.element.style.removeProperty('--stat-scale');
                statData.element.style.removeProperty('--stat-aura-opacity');
                statData.element.style.removeProperty('--stat-swash-scale');
                statData.element.style.removeProperty('--stat-orbit-opacity');
                statData.element.style.removeProperty('--stat-orbit-scale');
                statData.element.style.removeProperty('--stat-orbit-rotate');
                statData.element.style.removeProperty('--stat-rotate');
            });
        }

        function measureDesktopHeight() {
            if (!state.enhanced) return;

            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );
            var screenCount = Math.max(state.stats.length + 0.9, 4.6);
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
            var entryProgress = 1 - smoothstep(
                viewportHeight * 0.3,
                viewportHeight * 0.82,
                rect.top
            );
            var introEnd = 0.085;
            var storyEnd = 0.925;
            var segment = (storyEnd - introEnd) / state.stats.length;
            var sideProgress = smoothstep(
                introEnd * 0.46,
                introEnd + segment * 0.2,
                progress
            );
            var outroProgress = smoothstep(storyEnd, 0.997, progress);
            var approximateIndex = clamp(
                Math.floor(
                    (progress - introEnd + segment * 0.16) / segment
                ),
                0,
                state.stats.length - 1
            );
            var activeIndex = progress < introEnd * 0.55
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
                : smoothstep(-0.12, 0.24, activeLocal);
            var aboutSide = activeIndex <= 0
                ? lerp(0, targetSide, sideBlend)
                : lerp(previousSide, targetSide, sideBlend);
            var aboutHorizontal = aboutSide * viewportWidth * 0.275;
            var prePinCompensation = -clamp(
                rect.top,
                0,
                viewportHeight
            ) * 0.52;
            var aboutEnterY = lerp(
                viewportHeight * 0.18,
                0,
                easeOutCubic(entryProgress)
            );
            var aboutExitY = -viewportHeight * 0.46 * outroProgress;
            var aboutOpacity = smoothstep(
                0.025,
                0.38,
                entryProgress
            ) * (1 - smoothstep(0.94, 1, progress));
            var aboutScale = lerp(
                0.86,
                1,
                easeOutCubic(entryProgress)
            ) - sideProgress * 0.44 - outroProgress * 0.08;
            var aboutRotation = aboutSide * -1.7;
            var artProgress = easeOutCubic(
                smoothstep(0.02, 0.54, entryProgress)
            );
            var detailProgress = smoothstep(
                0.16,
                0.68,
                entryProgress
            );
            var wordProgress = smoothstep(
                0.04,
                0.56,
                entryProgress
            );

            root.style.setProperty(
                '--about-x',
                aboutHorizontal.toFixed(2) + 'px'
            );
            root.style.setProperty(
                '--about-y',
                (
                    prePinCompensation +
                    aboutEnterY +
                    aboutExitY
                ).toFixed(2) + 'px'
            );
            root.style.setProperty(
                '--about-rotate',
                aboutRotation.toFixed(2) + 'deg'
            );
            root.style.setProperty(
                '--about-z-rotate',
                (aboutSide * 0.42).toFixed(2) + 'deg'
            );
            setNumberProperty(root, '--about-opacity', aboutOpacity);
            setNumberProperty(
                root,
                '--about-scale',
                Math.max(aboutScale, 0.43)
            );
            setNumberProperty(
                root,
                '--about-word-opacity',
                wordProgress * (1 - outroProgress * 0.74)
            );
            setNumberProperty(
                root,
                '--about-art-opacity',
                smoothstep(0.02, 0.34, entryProgress) *
                    (1 - outroProgress * 0.62)
            );
            setNumberProperty(
                root,
                '--about-art-scale',
                lerp(0.72, 1, artProgress)
            );
            setNumberProperty(
                root,
                '--about-detail-opacity',
                detailProgress *
                    (1 - sideProgress * 0.24) *
                    (1 - outroProgress * 0.82)
            );
            root.style.setProperty(
                '--about-copy-y',
                lerp(34, 0, easeOutCubic(detailProgress)).toFixed(2) +
                    'px'
            );
            root.style.setProperty(
                '--about-word-y',
                lerp(48, 0, easeOutCubic(wordProgress)).toFixed(2) +
                    'px'
            );
            root.style.setProperty(
                '--about-word-clip',
                ((1 - wordProgress) * 100).toFixed(2) + '%'
            );
            setNumberProperty(
                root,
                '--about-line-scale',
                smoothstep(0.36, 0.9, entryProgress)
            );
            setNumberProperty(
                root,
                '--hint-opacity',
                smoothstep(0.42, 0.82, entryProgress) *
                    (1 - smoothstep(0.01, 0.065, progress))
            );
            setNumberProperty(
                root,
                '--progress-opacity',
                smoothstep(introEnd * 0.58, introEnd + 0.025, progress) *
                    (1 - smoothstep(0.92, 0.98, progress))
            );

            var mostVisibleIndex = -1;
            var mostVisibleOpacity = 0;

            state.stats.forEach(function (statData, index) {
                var start = introEnd + index * segment;
                var local = (progress - start) / segment;
                var enter = smoothstep(-0.16, 0.2, local);
                var leave = 1 - smoothstep(0.72, 1.08, local);
                var opacity = enter * leave;
                var side = index % 2 === 0 ? 1 : -1;
                var targetX = side * viewportWidth * 0.275;
                var entryX = side * viewportWidth * 0.045 * (1 - enter);
                var entryY = viewportHeight * 0.21 * (1 - enter);
                var exitProgress = smoothstep(0.72, 1.08, local);
                var exitY = -viewportHeight * 0.22 * exitProgress;
                var scale = lerp(0.86, 1, enter) -
                    exitProgress * 0.07;
                var contentProgress = smoothstep(-0.03, 0.35, local);
                var labelProgress = smoothstep(0.05, 0.49, local);
                var auraProgress = smoothstep(-0.1, 0.3, local);
                var orbitProgress = smoothstep(-0.02, 0.42, local);

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
                    opacity * auraProgress
                );
                setNumberProperty(
                    statData.element,
                    '--stat-swash-scale',
                    lerp(0.72, 1, auraProgress)
                );
                setNumberProperty(
                    statData.element,
                    '--stat-orbit-opacity',
                    opacity * orbitProgress * 0.7
                );
                setNumberProperty(
                    statData.element,
                    '--stat-orbit-scale',
                    lerp(0.72, 1, orbitProgress)
                );
                statData.element.style.setProperty(
                    '--stat-orbit-rotate',
                    (
                        side * lerp(20, -8, orbitProgress) +
                        exitProgress * side * 10
                    ).toFixed(2) + 'deg'
                );
                statData.element.style.setProperty(
                    '--stat-rotate',
                    (
                        side * lerp(2.2, 0, enter) -
                        side * exitProgress * 1.4
                    ).toFixed(2) + 'deg'
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
            var shiftX = normalizedX * 9;
            var shiftY = normalizedY * 7;

            root.style.setProperty(
                '--pointer-x',
                shiftX.toFixed(2) + 'px'
            );
            root.style.setProperty(
                '--pointer-y',
                shiftY.toFixed(2) + 'px'
            );
            root.style.setProperty(
                '--pointer-x-reverse',
                (-shiftX * 0.72).toFixed(2) + 'px'
            );
            root.style.setProperty(
                '--pointer-y-reverse',
                (-shiftY * 0.72).toFixed(2) + 'px'
            );
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
            var duration = 950;

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
                    threshold: 0.18,
                    rootMargin: '0px 0px -4% 0px'
                }
            );

            statElements.forEach(function (statElement) {
                state.mobileObserver.observe(statElement);
            });
        }

        function teardownCurrentMode() {
            window.removeEventListener('scroll', requestDesktopRender);
            window.removeEventListener('resize', onDesktopResize);
            pointerTarget.removeEventListener(
                'pointermove',
                requestPointerRender
            );
            pointerTarget.removeEventListener('pointerleave', resetPointer);
            window.clearTimeout(state.resizeTimer);
            resetPointer();

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
                pointerTarget.addEventListener(
                    'pointermove',
                    requestPointerRender,
                    { passive: true }
                );
                pointerTarget.addEventListener(
                    'pointerleave',
                    resetPointer,
                    { passive: true }
                );
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
