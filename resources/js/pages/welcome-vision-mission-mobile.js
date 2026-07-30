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

    function localeKey(locale) {
        return String(locale || 'id').toLowerCase().split('-')[0];
    }

    function linePlan(locale, wordCount) {
        var key = localeKey(locale);

        if (key === 'id' && wordCount === 4) {
            return [[0, 1], [2, 3]];
        }

        if (key === 'en' && wordCount === 4) {
            return [[0], [1, 2], [3]];
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

    function restoreOriginalStructure(current) {
        if (!current || !current.line) return;

        clearChildren(current.line);
        current.originalNodes.forEach(function (node) {
            current.line.appendChild(node);
        });

        current.mobileLines = [];
        current.heading.classList.remove('is-mobile-composed');
    }

    function composeMobileLines(current) {
        if (!current || !mobileQuery.matches) return;

        var plan = linePlan(current.locale, current.words.length);
        var fragment = document.createDocumentFragment();
        var mobileLines = [];

        clearChildren(current.line);

        plan.forEach(function (wordIndexes, lineIndex) {
            var mobileLine = document.createElement('span');

            mobileLine.className = 'vision-mission-heading__mobile-line';
            mobileLine.setAttribute('data-vision-mobile-line', '');
            mobileLine.style.setProperty('--vision-mobile-line-index', String(lineIndex));

            if (localeKey(current.locale) === 'ar' && lineIndex === 1) {
                mobileLine.classList.add(
                    'vision-mission-heading__mobile-line--secondary'
                );
            }

            wordIndexes.forEach(function (wordIndex) {
                if (current.words[wordIndex]) {
                    mobileLine.appendChild(current.words[wordIndex]);
                }
            });

            mobileLines.push(mobileLine);
            fragment.appendChild(mobileLine);
        });

        current.line.appendChild(fragment);
        current.mobileLines = mobileLines;
        current.heading.classList.add('is-mobile-composed');
    }

    function measuredLineWidth(mobileLine) {
        var style = window.getComputedStyle(mobileLine);
        var marginStart = parseFloat(style.marginInlineStart) || 0;
        var marginEnd = parseFloat(style.marginInlineEnd) || 0;

        return mobileLine.getBoundingClientRect().width + marginStart + marginEnd;
    }

    function fitMobileLines(current) {
        if (!current || !mobileQuery.matches || !current.mobileLines.length) return;

        var maximumSize = 93;
        var available = current.title.clientWidth * 0.96;

        if (available < 1) return;

        current.heading.style.setProperty(
            '--vision-mobile-heading-size',
            maximumSize + 'px'
        );

        for (var pass = 0; pass < 2; pass += 1) {
            var widestLine = current.mobileLines.reduce(function (largest, mobileLine) {
                return Math.max(largest, measuredLineWidth(mobileLine));
            }, 0);

            if (widestLine < 1 || widestLine <= available) break;

            var currentSize = parseFloat(
                window.getComputedStyle(current.title).fontSize
            ) || maximumSize;
            var fittedSize = currentSize * (available / widestLine);

            current.heading.style.setProperty(
                '--vision-mobile-heading-size',
                fittedSize.toFixed(2) + 'px'
            );
        }
    }

    function revealMobileHeading(current) {
        if (!current || current.revealed) return;

        current.revealed = true;
        window.requestAnimationFrame(function () {
            if (state === current) {
                current.heading.classList.add('is-mobile-visible');
            }
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
            if (state === current && current.observer) {
                current.observer.observe(current.heading);
            }
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

        var current = {
            heading: heading,
            title: title,
            line: line,
            words: words,
            locale: heading.getAttribute('data-locale') || document.documentElement.lang || 'id',
            originalNodes: Array.prototype.slice.call(line.children),
            mobileLines: [],
            observer: null,
            revealed: false
        };

        state = current;

        words.forEach(function (word, index) {
            word.style.setProperty(
                '--vision-rainbow-offset',
                String(index * -34) + '%'
            );
        });

        composeMobileLines(current);
        heading.classList.remove('is-mobile-visible');
        heading.classList.add('is-mobile-ready');

        window.requestAnimationFrame(function () {
            if (state !== current) return;

            fitMobileLines(current);
            void heading.offsetWidth;
            observeMobileHeading(current);
        });

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                if (state === current) {
                    fitMobileLines(current);
                }
            });
        }
    }

    function deactivateMobileHeading() {
        if (!state) return;

        var current = state;

        if (current.observer) {
            current.observer.disconnect();
        }

        current.heading.classList.remove(
            'is-mobile-ready',
            'is-mobile-visible',
            'is-mobile-composed'
        );
        current.heading.style.removeProperty('--vision-mobile-heading-size');
        restoreOriginalStructure(current);
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
                if (state) fitMobileLines(state);
            }, 140);
        }, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
