(function () {
    'use strict';

    var mobileQuery = window.matchMedia('(max-width: 767px)');
    var reducedQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    var state = null;
    var resizeTimer = 0;

    function onQueryChange(query, listener) {
        if (typeof query.addEventListener === 'function') {
            query.addEventListener('change', listener);
            return;
        }

        query.addListener(listener);
    }

    function fitLargestWord(current) {
        if (!current || !mobileQuery.matches) return;

        var maximumSize = 93;
        var available = current.title.clientWidth * 0.98;

        if (available < 1) return;

        current.heading.style.setProperty(
            '--vision-mobile-heading-size',
            maximumSize + 'px'
        );

        var widestWord = current.words.reduce(function (largest, word) {
            return Math.max(largest, word.getBoundingClientRect().width);
        }, 0);

        if (widestWord < 1 || widestWord <= available) return;

        var fittedSize = maximumSize * (available / widestWord);
        current.heading.style.setProperty(
            '--vision-mobile-heading-size',
            fittedSize.toFixed(2) + 'px'
        );
    }

    function revealMobileHeading(current) {
        if (!current || current.revealed) return;

        current.revealed = true;
        window.requestAnimationFrame(function () {
            current.heading.classList.add('is-mobile-visible');
        });
    }

    function observeMobileHeading(current) {
        if (!current) return;

        if (reducedQuery.matches || !('IntersectionObserver' in window)) {
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    revealMobileHeading(current);
                });
            });
            return;
        }

        current.observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                revealMobileHeading(current);
                current.observer.disconnect();
                current.observer = null;
            });
        }, {
            threshold: 0.18,
            rootMargin: '0px 0px -8% 0px'
        });

        window.requestAnimationFrame(function () {
            if (!state || state !== current) return;
            current.observer.observe(current.heading);
        });
    }

    function activateMobileHeading() {
        if (!mobileQuery.matches || state) return;

        var section = document.querySelector('[data-vision-mission]');
        if (!section) return;

        var heading = section.querySelector('[data-vision-mission-heading]');
        if (!heading) return;

        var title = heading.querySelector('.vision-mission-heading__title');
        var words = Array.prototype.slice.call(
            heading.querySelectorAll('[data-vision-word]')
        );

        if (!title || !words.length) return;

        state = {
            heading: heading,
            title: title,
            words: words,
            observer: null,
            revealed: false
        };

        words.forEach(function (word, index) {
            word.style.setProperty(
                '--vision-rainbow-offset',
                String(index * -34) + '%'
            );
        });

        heading.classList.remove('is-mobile-visible');
        heading.classList.add('is-mobile-ready');

        window.requestAnimationFrame(function () {
            if (!state) return;
            fitLargestWord(state);
            void heading.offsetWidth;
            observeMobileHeading(state);
        });

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                if (state) fitLargestWord(state);
            });
        }
    }

    function deactivateMobileHeading() {
        if (!state) return;

        if (state.observer) {
            state.observer.disconnect();
        }

        state.heading.classList.remove(
            'is-mobile-ready',
            'is-mobile-visible'
        );
        state.heading.style.removeProperty('--vision-mobile-heading-size');
        state = null;
    }

    function handleModeChange() {
        if (mobileQuery.matches) {
            activateMobileHeading();
            return;
        }

        deactivateMobileHeading();
    }

    function initialise() {
        handleModeChange();

        onQueryChange(mobileQuery, handleModeChange);
        onQueryChange(reducedQuery, function () {
            if (state && reducedQuery.matches) {
                revealMobileHeading(state);
            }
        });

        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                if (state) fitLargestWord(state);
            }, 140);
        }, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
