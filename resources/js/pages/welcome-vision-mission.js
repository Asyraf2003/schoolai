(function () {
    'use strict';

    function onQueryChange(query, listener) {
        if (typeof query.addEventListener === 'function') {
            query.addEventListener('change', listener);
            return;
        }

        query.addListener(listener);
    }

    function initialise() {
        var section = document.querySelector('[data-vision-mission]');
        if (!section) return;

        var heading = section.querySelector('[data-vision-mission-heading]');
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

        if (!heading) return;

        var locale = heading.getAttribute('data-locale') || document.documentElement.lang || 'id';
        var words = Array.prototype.slice.call(
            heading.querySelectorAll('[data-vision-word]')
        );
        var desktopQuery = window.matchMedia('(min-width: 1181px)');
        var reducedQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        var rollTimer = 0;
        var revealed = false;

        function prepareLatinCharacters() {
            if (locale === 'ar') return;

            words.forEach(function (word) {
                var characters = Array.from(word.textContent);
                var fragment = document.createDocumentFragment();

                characters.forEach(function (character) {
                    var windowElement = document.createElement('span');
                    var track = document.createElement('span');

                    windowElement.className = 'vision-mission-heading__char';
                    windowElement.setAttribute('data-vision-char', '');
                    windowElement.setAttribute('data-original', character);

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

                word.textContent = '';
                word.appendChild(fragment);
            });
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

            character.classList.remove('is-rolling');
            void character.offsetWidth;
            character.classList.add('is-rolling');
            track.addEventListener('animationend', cleanup, { once: true });
            window.setTimeout(cleanup, 900);
        }

        function scheduleRoll() {
            window.clearTimeout(rollTimer);
            if (!revealed || locale === 'ar' || reducedQuery.matches || !desktopQuery.matches) {
                return;
            }

            rollTimer = window.setTimeout(runLetterRoll, 4200 + Math.random() * 2600);
        }

        function runLetterRoll() {
            if (document.hidden || !desktopQuery.matches || reducedQuery.matches) {
                scheduleRoll();
                return;
            }

            var eligible = Array.prototype.slice.call(
                heading.querySelectorAll('[data-vision-char]')
            ).filter(function (character) {
                return /^[A-Za-z]$/.test(character.getAttribute('data-original') || '');
            });

            var count = Math.min(eligible.length, 2 + Math.floor(Math.random() * 2));
            var selected = eligible.sort(function () { return Math.random() - 0.5; }).slice(0, count);

            selected.forEach(function (character, index) {
                window.setTimeout(function () { rollCharacter(character); }, index * 90);
            });

            scheduleRoll();
        }

        function revealHeading() {
            if (revealed) return;
            revealed = true;
            heading.classList.add('is-visible');
            scheduleRoll();
        }

        prepareLatinCharacters();
        heading.classList.add('is-ready');

        if (reducedQuery.matches || !('IntersectionObserver' in window)) {
            revealHeading();
        } else {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    revealHeading();
                    observer.disconnect();
                });
            }, {
                threshold: 0.3,
                rootMargin: '0px 0px -8% 0px'
            });

            observer.observe(heading);
        }

        onQueryChange(desktopQuery, scheduleRoll);
        onQueryChange(reducedQuery, scheduleRoll);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();