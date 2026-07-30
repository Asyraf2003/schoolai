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

    function clamp(value, minimum, maximum) {
        return Math.min(maximum, Math.max(minimum, value));
    }

    function fitMobileHeading(current) {
        if (!current || !mobileQuery.matches) return;

        var title = current.title;
        var line = current.line;
        var words = current.words;
        var available = title.clientWidth;

        if (available < 1 || !words.length) return;

        line.style.setProperty('--vision-mobile-scale', '1');
        line.style.setProperty('--vision-mobile-word-gap', '0px');

        var wordsWidth = words.reduce(function (total, word) {
            return total + word.getBoundingClientRect().width;
        }, 0);

        if (wordsWidth < 1) return;

        var visibleGap = clamp(available * 0.028, 8, 14);
        var gapCount = Math.max(0, words.length - 1);
        var usableWidth = Math.max(1, (available * 0.985) - (visibleGap * gapCount));
        var scale = Math.min(1, usableWidth / wordsWidth);
        var sourceGap = scale > 0 ? visibleGap / scale : visibleGap;

        line.style.setProperty('--vision-mobile-scale', scale.toFixed(4));
        line.style.setProperty('--vision-mobile-word-gap', sourceGap.toFixed(2) + 'px');
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
            threshold: 0.28,
            rootMargin: '0px 0px -12% 0px'
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
        var line = heading.querySelector('.vision-mission-heading__line');
        var words = Array.prototype.slice.call(
            heading.querySelectorAll('[data-vision-word]')
        );

        if (!title || !line || !words.length) return;

        state = {
            heading: heading,
            title: title,
            line: line,
            words: words,
            observer: null,
            revealed: false
        };

        words.forEach(function (word, index) {
            word.style.setProperty('--vision-rainbow-offset', String(index * -34) + '%');
        });

        heading.classList.remove('is-mobile-visible');
        heading.classList.add('is-mobile-ready');

        window.requestAnimationFrame(function () {
            if (!state) return;
            fitMobileHeading(state);
            void heading.offsetWidth;
            observeMobileHeading(state);
        });

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                if (state) fitMobileHeading(state);
            });
        }
    }

    function deactivateMobileHeading() {
        if (!state) return;

        if (state.observer) {
            state.observer.disconnect();
        }

        state.heading.classList.remove('is-mobile-ready', 'is-mobile-visible');
        state.line.style.removeProperty('--vision-mobile-scale');
        state.line.style.removeProperty('--vision-mobile-word-gap');
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
            if (!state) return;

            if (reducedQuery.matches) {
                revealMobileHeading(state);
            }
        });

        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                if (state) fitMobileHeading(state);
            }, 140);
        }, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();