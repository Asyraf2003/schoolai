(function () {
    'use strict';

    function onQueryChange(query, listener) {
        if (typeof query.addEventListener === 'function') {
            query.addEventListener('change', listener);
            return;
        }

        query.addListener(listener);
    }

    function initialiseMissionCards(section) {
        var missionCards = Array.prototype.slice.call(
            section.querySelectorAll('[data-mission-card]')
        );

        missionCards.forEach(function (card) {
            function activate() {
                missionCards.forEach(function (candidate) {
                    var active = candidate === card;
                    candidate.classList.toggle('is-active', active);
                    candidate.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
            }

            card.addEventListener('click', activate);
            card.addEventListener('focus', activate);
            card.addEventListener('mouseenter', function () {
                if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
                    activate();
                }
            });
        });
    }

    function localeKey(locale) {
        return String(locale || 'id').toLowerCase().split('-')[0];
    }

    function linePlan(locale, wordCount) {
        var key = localeKey(locale);

        if ((key === 'id' || key === 'en') && wordCount === 4) {
            return [[0, 1], [2, 3]];
        }

        if (key === 'ar' && wordCount === 3) {
            return [[0, 1], [2]];
        }

        var splitAt = Math.ceil(wordCount / 2);
        var first = [];
        var second = [];

        for (var index = 0; index < wordCount; index += 1) {
            (index < splitAt ? first : second).push(index);
        }

        return second.length ? [first, second] : [first];
    }

    function clearChildren(element) {
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    }

    function initialise() {
        var section = document.querySelector('[data-vision-mission]');
        if (!section) return;

        initialiseMissionCards(section);

        var heading = section.querySelector('[data-vision-mission-heading]');
        if (!heading) return;

        var title = heading.querySelector('.vision-mission-heading__title');
        var line = heading.querySelector('.vision-mission-heading__line');
        var words = Array.prototype.slice.call(
            heading.querySelectorAll('[data-vision-word]')
        );

        if (!title || !line || !words.length) return;

        var locale = heading.getAttribute('data-locale') || document.documentElement.lang || 'id';
        var desktopQuery = window.matchMedia('(min-width: 1181px)');
        var tabletQuery = window.matchMedia('(min-width: 768px) and (max-width: 1180px)');
        var mobileQuery = window.matchMedia('(max-width: 767px)');
        var reducedQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        var originalNodes = Array.prototype.slice.call(line.children);
        var layoutLines = [];
        var observer = null;
        var rollTimer = 0;
        var resizeTimer = 0;
        var revealWaitTimer = 0;
        var reelsPrepared = false;
        var fontsReady = !document.fonts || !document.fonts.ready;
        var active = false;
        var revealRequested = false;
        var revealStarted = false;
        var revealed = false;
        var firstRollPending = true;

        words.forEach(function (word, index) {
            word.dataset.originalText = word.textContent.trim();
            word.dataset.visionWordIndex = String(index);
        });

        function restorePlainWords() {
            words.forEach(function (word) {
                word.classList.remove('has-reels');
                word.textContent = word.dataset.originalText || '';
            });

            reelsPrepared = false;
        }

        function restoreOriginalStructure() {
            clearChildren(line);
            originalNodes.forEach(function (node) {
                line.appendChild(node);
            });

            layoutLines = [];
            heading.classList.remove('is-layout-composed');
        }

        function composeLayout() {
            var plan = linePlan(locale, words.length);
            var fragment = document.createDocumentFragment();

            restoreOriginalStructure();
            clearChildren(line);

            plan.forEach(function (wordIndexes, lineIndex) {
                var layoutLine = document.createElement('span');

                layoutLine.className = 'vision-mission-heading__layout-line';
                layoutLine.setAttribute('data-vision-layout-line', '');

                if (lineIndex > 0) {
                    layoutLine.classList.add(
                        'vision-mission-heading__layout-line--secondary'
                    );
                }

                wordIndexes.forEach(function (wordIndex) {
                    if (words[wordIndex]) {
                        layoutLine.appendChild(words[wordIndex]);
                    }
                });

                layoutLines.push(layoutLine);
                fragment.appendChild(layoutLine);
            });

            line.appendChild(fragment);
            heading.classList.add('is-layout-composed');
        }

        function clamp(value, minimum, maximum) {
            return Math.min(maximum, Math.max(minimum, value));
        }

        function headingSizeLimits() {
            if (desktopQuery.matches) {
                return { minimum: 52, maximum: 180 };
            }

            return { minimum: 34, maximum: 104 };
        }

        function availableLineWidth() {
            var style = window.getComputedStyle(line);
            var paddingStart = parseFloat(style.paddingInlineStart) || 0;
            var paddingEnd = parseFloat(style.paddingInlineEnd) || 0;

            return Math.max(0, line.clientWidth - paddingStart - paddingEnd);
        }

        function measuredLineWidth(layoutLine) {
            var style = window.getComputedStyle(layoutLine);
            var gap = parseFloat(style.columnGap || style.gap) || 0;
            var marginStart = parseFloat(style.marginInlineStart) || 0;
            var marginEnd = parseFloat(style.marginInlineEnd) || 0;
            var children = Array.prototype.slice.call(
                layoutLine.querySelectorAll(':scope > [data-vision-word]')
            );
            var contentWidth = children.reduce(function (total, word) {
                return total + Math.max(
                    word.scrollWidth || 0,
                    word.getBoundingClientRect().width
                );
            }, 0);

            contentWidth += gap * Math.max(0, children.length - 1);

            return Math.max(
                contentWidth,
                layoutLine.scrollWidth || 0,
                layoutLine.getBoundingClientRect().width
            ) + marginStart + marginEnd;
        }

        function fitHeadingToLines() {
            if (!active || mobileQuery.matches || !layoutLines.length) return;

            title.style.removeProperty('--vision-heading-fitted-size');

            var available = availableLineWidth();
            if (available < 1) return;

            var limits = headingSizeLimits();
            var safetyRatio = localeKey(locale) === 'ar' ? 0.93 : 0.96;

            for (var pass = 0; pass < 3; pass += 1) {
                var widestLine = layoutLines.reduce(function (largest, layoutLine) {
                    return Math.max(largest, measuredLineWidth(layoutLine));
                }, 0);

                if (widestLine < 1) return;

                var currentSize = parseFloat(
                    window.getComputedStyle(title).fontSize
                ) || limits.minimum;
                var target = clamp(
                    currentSize * ((available * safetyRatio) / widestLine),
                    limits.minimum,
                    limits.maximum
                );

                title.style.setProperty(
                    '--vision-heading-fitted-size',
                    target.toFixed(2) + 'px'
                );

                if (Math.abs(widestLine - (available * safetyRatio)) < 1) {
                    break;
                }
            }
        }

        function measureCharacters(characters) {
            var measure = document.createElement('span');
            var widths = [];

            measure.className = 'vision-mission-heading__measure';
            measure.setAttribute('aria-hidden', 'true');
            title.appendChild(measure);

            characters.forEach(function (character) {
                measure.textContent = character;
                widths.push(measure.getBoundingClientRect().width);
            });

            measure.remove();

            if (widths.some(function (width) {
                return !Number.isFinite(width) || width < 1;
            })) {
                return null;
            }

            return widths;
        }

        function buildDesktopReels() {
            if (
                reelsPrepared
                || !active
                || localeKey(locale) === 'ar'
                || reducedQuery.matches
                || !desktopQuery.matches
                || !fontsReady
            ) {
                return reelsPrepared;
            }

            var plans = words.map(function (word, wordIndex) {
                var original = word.dataset.originalText || '';
                var displayText = original.toLocaleUpperCase(
                    localeKey(locale) === 'en' ? 'en-US' : 'id-ID'
                );
                var characters = Array.from(displayText);
                var widths = measureCharacters(characters);

                if (!widths) return null;

                return {
                    word: word,
                    wordIndex: wordIndex,
                    characters: characters,
                    widths: widths
                };
            });

            if (plans.some(function (plan) { return !plan; })) {
                restorePlainWords();
                return false;
            }

            plans.forEach(function (plan) {
                var fragment = document.createDocumentFragment();

                plan.characters.forEach(function (character, characterIndex) {
                    var windowElement = document.createElement('span');
                    var track = document.createElement('span');
                    var measuredWidth = Math.ceil(
                        plan.widths[characterIndex] * 100
                    ) / 100;
                    var rainbowOffset = -(
                        (plan.wordIndex * 34) + (characterIndex * 8)
                    );

                    windowElement.className = 'vision-mission-heading__char';
                    windowElement.setAttribute('data-vision-char', '');
                    windowElement.setAttribute('data-original', character);
                    windowElement.style.setProperty(
                        '--vision-char-width',
                        measuredWidth + 'px'
                    );
                    windowElement.style.setProperty(
                        '--vision-rainbow-offset',
                        rainbowOffset + '%'
                    );

                    track.className = 'vision-mission-heading__char-track';
                    track.setAttribute('aria-hidden', 'true');

                    for (var index = 0; index < 5; index += 1) {
                        var glyph = document.createElement('span');
                        glyph.className = 'vision-mission-heading__char-glyph';
                        glyph.textContent = character;
                        track.appendChild(glyph);
                    }

                    windowElement.appendChild(track);
                    fragment.appendChild(windowElement);
                });

                plan.word.textContent = '';
                plan.word.appendChild(fragment);
                plan.word.classList.add('has-reels');
            });

            reelsPrepared = true;
            return true;
        }

        function canRoll() {
            return active
                && revealed
                && reelsPrepared
                && localeKey(locale) !== 'ar'
                && desktopQuery.matches
                && !reducedQuery.matches;
        }

        function rollCharacter(character) {
            if (character.classList.contains('is-rolling')) return;

            var track = character.querySelector('.vision-mission-heading__char-track');
            if (!track) return;

            var finished = false;

            function cleanup() {
                if (finished) return;
                finished = true;
                character.classList.remove('is-rolling');
            }

            void character.offsetWidth;
            character.classList.add('is-rolling');
            track.addEventListener('animationend', cleanup, { once: true });
            window.setTimeout(cleanup, 1100);
        }

        function scheduleRoll() {
            window.clearTimeout(rollTimer);
            if (!canRoll()) return;

            rollTimer = window.setTimeout(
                runLetterRoll,
                firstRollPending ? 900 : 3800 + Math.random() * 2200
            );
        }

        function runLetterRoll() {
            if (document.hidden || !canRoll()) {
                scheduleRoll();
                return;
            }

            var eligible = Array.prototype.slice.call(
                heading.querySelectorAll('[data-vision-char]')
            ).filter(function (character) {
                return /^[A-Z]$/.test(
                    character.getAttribute('data-original') || ''
                );
            });

            for (var index = eligible.length - 1; index > 0; index -= 1) {
                var randomIndex = Math.floor(Math.random() * (index + 1));
                var temporary = eligible[index];
                eligible[index] = eligible[randomIndex];
                eligible[randomIndex] = temporary;
            }

            var count = Math.min(
                eligible.length,
                2 + Math.floor(Math.random() * 2)
            );

            firstRollPending = false;

            eligible.slice(0, count).forEach(function (character, index) {
                window.setTimeout(function () {
                    rollCharacter(character);
                }, index * 110);
            });

            scheduleRoll();
        }

        function syncLayout(resetFirstRoll) {
            window.clearTimeout(rollTimer);
            restorePlainWords();

            if (!active || mobileQuery.matches) return;

            if (!layoutLines.length) {
                composeLayout();
            }

            fitHeadingToLines();

            if (
                fontsReady
                && desktopQuery.matches
                && localeKey(locale) !== 'ar'
                && !reducedQuery.matches
            ) {
                buildDesktopReels();
            }

            if (resetFirstRoll) {
                firstRollPending = true;
            }

            if (revealed) {
                scheduleRoll();
            }
        }

        function disconnectObserver() {
            if (!observer) return;
            observer.disconnect();
            observer = null;
        }

        function completeReveal() {
            if (!active || revealStarted) return;
            revealStarted = true;

            syncLayout(true);

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    if (!active) return;
                    revealed = true;
                    heading.classList.add('is-visible');
                    scheduleRoll();
                });
            });
        }

        function requestReveal() {
            if (!active) return;
            revealRequested = true;

            if (fontsReady || reducedQuery.matches) {
                completeReveal();
                return;
            }

            window.clearTimeout(revealWaitTimer);
            revealWaitTimer = window.setTimeout(completeReveal, 800);
        }

        function observeHeading() {
            disconnectObserver();

            if (reducedQuery.matches || !('IntersectionObserver' in window)) {
                requestReveal();
                return;
            }

            observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    requestReveal();
                    disconnectObserver();
                });
            }, {
                threshold: 0.16,
                rootMargin: '0px 0px -4% 0px'
            });

            window.requestAnimationFrame(function () {
                if (active && observer) observer.observe(heading);
            });
        }

        function activateHeading() {
            if (active || mobileQuery.matches) return;

            active = true;
            revealRequested = false;
            revealStarted = false;
            revealed = false;
            firstRollPending = true;

            composeLayout();
            heading.classList.remove('is-visible');
            heading.classList.add('is-ready');
            syncLayout(true);
            observeHeading();
        }

        function deactivateHeading() {
            if (!active) return;

            active = false;
            window.clearTimeout(rollTimer);
            window.clearTimeout(revealWaitTimer);
            disconnectObserver();
            restorePlainWords();
            restoreOriginalStructure();
            title.style.removeProperty('--vision-heading-fitted-size');
            heading.classList.remove('is-ready', 'is-visible');
        }

        function handleModeChange() {
            if (mobileQuery.matches) {
                deactivateHeading();
                return;
            }

            window.requestAnimationFrame(function () {
                if (mobileQuery.matches) return;

                if (!active) {
                    activateHeading();
                    return;
                }

                syncLayout(true);
            });
        }

        if (!mobileQuery.matches) {
            activateHeading();
        }

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                fontsReady = true;
                window.clearTimeout(revealWaitTimer);

                if (!active) return;

                if (revealRequested && !revealStarted) {
                    completeReveal();
                    return;
                }

                syncLayout(true);
            });
        } else {
            fontsReady = true;
            if (active) syncLayout(true);
        }

        onQueryChange(desktopQuery, handleModeChange);
        onQueryChange(tabletQuery, handleModeChange);
        onQueryChange(mobileQuery, handleModeChange);
        onQueryChange(reducedQuery, function () {
            if (active) syncLayout(true);
        });

        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                if (active) syncLayout(false);
            }, 180);
        }, { passive: true });

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) scheduleRoll();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
