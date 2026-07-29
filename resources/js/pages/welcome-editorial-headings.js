/* Replay each shared homepage heading at the 20% viewport crossing. */
(function () {
    'use strict';

    function initialise() {
        var headings = Array.prototype.slice.call(
            document.querySelectorAll('[data-editorial-heading]')
        );
        if (!headings.length) return;

        var reducedMotion = window.matchMedia
            && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var triggerLine = window.innerHeight * 0.8;
        var lastScrollY = window.scrollY || window.pageYOffset || 0;
        var ticking = false;

        var items = headings.map(function (heading) {
            var section = heading.closest('section') || heading;
            var top = section.getBoundingClientRect().top;

            heading.classList.add('welcome-editorial-heading--ready');

            return {
                heading: heading,
                section: section,
                previousTop: top,
                armed: top > triggerLine
            };
        });

        function prepare(item) {
            item.heading.classList.remove(
                'welcome-editorial-heading--animated',
                'welcome-editorial-heading--static'
            );
        }

        function showStatic(item) {
            item.heading.classList.remove('welcome-editorial-heading--animated');
            item.heading.classList.add('welcome-editorial-heading--static');
        }

        function replay(item) {
            prepare(item);
            void item.heading.offsetWidth;
            item.heading.classList.add('welcome-editorial-heading--animated');
        }

        function resetPresentation() {
            triggerLine = window.innerHeight * 0.8;

            items.forEach(function (item) {
                var top = item.section.getBoundingClientRect().top;
                item.previousTop = top;
                item.armed = top > triggerLine;

                if (item.armed) prepare(item);
                else showStatic(item);
            });
        }

        function update() {
            var currentScrollY = window.scrollY || window.pageYOffset || 0;
            var delta = currentScrollY - lastScrollY;

            items.forEach(function (item) {
                var currentTop = item.section.getBoundingClientRect().top;

                if (delta > 1) {
                    if (
                        item.armed
                        && item.previousTop > triggerLine
                        && currentTop <= triggerLine
                    ) {
                        replay(item);
                        item.armed = false;
                    }
                } else if (delta < -1) {
                    if (currentTop > triggerLine) {
                        item.armed = true;
                        prepare(item);
                    } else {
                        showStatic(item);
                    }
                }

                item.previousTop = currentTop;
            });

            lastScrollY = currentScrollY;
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(update);
        }

        if (reducedMotion) {
            items.forEach(showStatic);
            return;
        }

        resetPresentation();
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', resetPresentation, { passive: true });
        window.addEventListener('pageshow', resetPresentation, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
