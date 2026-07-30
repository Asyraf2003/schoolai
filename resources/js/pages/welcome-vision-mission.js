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
        var locale = heading.getAttribute('data-locale') || document.documentElement.lang || 'id';
        var desktopQuery = window.matchMedia('(min-width: 1181px)');
        var tabletQuery = window.matchMedia('(min-width: 768px) and (max-width: 1180px)');
        var reducedQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        var rollTimer = 0;
        var resizeTimer = 0;
        var revealWaitTimer = 0;
        var reelsPrepared = false;
        var fontsReady = !document.fonts || !document.fonts.ready;
        var revealRequested = false;
        var revealStarted = false;
        var revealed = false;
        var firstRollPending = true;

        words.forEach(function (word, index) {
            word.dataset.originalText = word.textContent.trim();
            word.style.setProperty('--vision-rainbow-offset', String(index * -34) + '%');
        });

        function restorePlainWords() {
            words.forEach(function (word) {
                word.classList.remove('has-reels');
                word.textContent = word.dataset.originalText || '';
            });

            reelsPrepared = false;
        }

        function clamp(value, minimum, maximum) {
            return Math.min(maximum, Math.max(minimum, value));
        }

        function headingSizeLimits() {
            if (desktopQuery.matches) {
                return { minimum: 48, maximum: 180 };
            }

            if (tabletQuery.matches) {
                return { minimum: 30, maximum: 96 };
            }

            return { minimum: 16, maximum: 42 };
        }

        function availableLineWidth() {
            if (!line) return 0;

            var style = window.getComputedStyle(line);
            var paddingStart = parseFloat(style.paddingInlineStart) || 0;
            var paddingEnd = parseFloat(style.paddingInlineEnd) || 0;

            return Math.max(0, line.clientWidth - paddingStart - paddingEnd);
        }

        function plainWordsWidth() {
            if (!line || !words.length) return 0;

            var style = window.getComputedStyle(line);
            var gap = parseFloat(style.columnGap || style.gap) || 0;
            var width = words.reduce(function (total, word) {
                return total + word.getBoundingClientRect().width;
            }, 0);

            return width + (gap * Math.max(0, words.length - 1));
        }

        function fitHeadingToLine() {
            if (!title || !line || !words.length) return;

            title.style.removeProperty('--vision-heading-fitted-size');

            var available = availableLineWidth();
            var content = plainWordsWidth();
            if (available < 1 || content < 1) return;

            var limits = headingSizeLimits();
            var safetyRatio = locale === 'ar' ? 0.94 : 0.98;
            var currentSize = parseFloat(window.getComputedStyle(title).fontSize) || limits.minimum;
            var target = clamp(
                currentSize * ((available * safetyRatio) / content),
                limits.minimum,
                limits.maximum
            );

            title.style.setProperty('--vision-heading-fitted-size', target.toFixed(2) + 'px');

            var correctedContent = plainWordsWidth();
            if (correctedContent < 1) return;

            var correctedSize = parseFloat(window.getComputedStyle(title).fontSize) || target;
            var correctedTarget = clamp(
                correctedSize * ((available * safetyRatio) / correctedContent),
                limits.minimum,
                limits.maximum
            );

            title.style.setProperty('--vision-heading-fitted-size', correctedTarget.toFixed(2) + 'px');
        }

        function measureCharacters(characters) {
            if (!title) return null;

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
                || locale === 'ar'
                || reducedQuery.matches
                || !desktopQuery.matches
                || !fontsReady
            ) {
                return reelsPrepared;
            }

            var plans = words.map(function (word, wordIndex) {
                var original = word.dataset.originalText || '';
                var displayText = original.toLocaleUpperCase(locale === 'en' ? 'en-US' : 'id-ID');
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
                    var measuredWidth = Math.ceil(plan.widths[characterIndex] * 100) / 100;
                    var rainbowOffset = -((plan.wordIndex * 34) + (characterIndex * 8));

                    windowElement.className = 'vision-mission-heading__char';
                    windowElement.setAttribute('data-vision-char', '');
                    windowElement.setAttribute('data-original', character);
                    windowElement.style.setProperty('--vision-char-width', measuredWidth + 'px');
                    windowElement.style.setProperty('--vision-rainbow-offset', rainbowOffset + '%');

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
            return revealed
                && reelsPrepared
                && locale !== 'ar'
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

            var delay = firstRollPending
                ? 900
                : 3800 + Math.random() * 2200;

            rollTimer = window.setTimeout(runLetterRoll, delay);
        }

        function runLetterRoll() {
            if (document.hidden || !canRoll()) {
                scheduleRoll();
                return;
            }

            var eligible = Array.prototype.slice.call(
                heading.querySelectorAll('[data-vision-char]')
            ).filter(function (character) {
                return /^[A-Z]$/.test(character.getAttribute('data-original') || '');
            });

            for (var index = eligible.length - 1; index > 0; index -= 1) {
                var randomIndex = Math.floor(Math.random() * (index + 1));
                var temporary = eligible[index];
                eligible[index] = eligible[randomIndex];
                eligible[randomIndex] = temporary;
            }

            var count = Math.min(eligible.length, 2 + Math.floor(Math.random() * 2));
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
            fitHeadingToLine();

            if (
                fontsReady
                && desktopQuery.matches
                && locale !== 'ar'
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

        function completeReveal() {
            if (revealStarted) return;
            revealStarted = true;

            syncLayout(true);

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    revealed = true;
                    heading.classList.add('is-visible');
                    scheduleRoll();
                });
            });
        }

        function requestReveal() {
            revealRequested = true;

            if (fontsReady || reducedQuery.matches) {
                completeReveal();
                return;
            }

            window.clearTimeout(revealWaitTimer);
            revealWaitTimer = window.setTimeout(completeReveal, 800);
        }

        heading.classList.add('is-ready');

        if (reducedQuery.matches || !('IntersectionObserver' in window)) {
            requestReveal();
        } else {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    requestReveal();
                    observer.disconnect();
                });
            }, {
                threshold: 0.16,
                rootMargin: '0px 0px -4% 0px'
            });

            window.requestAnimationFrame(function () {
                observer.observe(heading);
            });
        }

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                fontsReady = true;
                window.clearTimeout(revealWaitTimer);

                if (revealRequested && !revealStarted) {
                    completeReveal();
                    return;
                }

                syncLayout(true);
            });
        } else {
            fontsReady = true;
            syncLayout(true);
        }

        function handleModeChange() {
            syncLayout(true);
        }

        onQueryChange(desktopQuery, handleModeChange);
        onQueryChange(tabletQuery, handleModeChange);
        onQueryChange(reducedQuery, handleModeChange);

        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                syncLayout(false);
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