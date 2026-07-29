/* Replay when the gallery section crosses 20% into the viewport while scrolling down. */
(function () {
    'use strict';

    function initialise() {
        var heading = document.querySelector('[data-gallery-heading]');
        if (!heading) return;

        var section = heading.closest('.galeri-section') || heading;
        var reducedMotion = window.matchMedia
            && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        heading.classList.add('gallery-heading-motion--ready');

        if (reducedMotion) {
            heading.classList.add('gallery-heading-motion--static');
            return;
        }

        var lastScrollY = window.scrollY || window.pageYOffset || 0;
        var previousTop = section.getBoundingClientRect().top;
        var triggerLine = window.innerHeight * 0.8;
        var armed = previousTop > triggerLine;
        var ticking = false;

        function prepare() {
            heading.classList.remove(
                'gallery-heading-motion--animated',
                'gallery-heading-motion--static'
            );
        }

        function showStatic() {
            heading.classList.remove('gallery-heading-motion--animated');
            heading.classList.add('gallery-heading-motion--static');
        }

        function replay() {
            prepare();
            void heading.offsetWidth;
            heading.classList.add('gallery-heading-motion--animated');
        }

        function update() {
            var currentScrollY = window.scrollY || window.pageYOffset || 0;
            var delta = currentScrollY - lastScrollY;
            var currentTop = section.getBoundingClientRect().top;

            if (delta > 1) {
                if (armed && previousTop > triggerLine && currentTop <= triggerLine) {
                    replay();
                    armed = false;
                }
            } else if (delta < -1) {
                if (currentTop > triggerLine) {
                    armed = true;
                    prepare();
                } else {
                    showStatic();
                }
            }

            lastScrollY = currentScrollY;
            previousTop = currentTop;
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(update);
        }

        function recalculate() {
            triggerLine = window.innerHeight * 0.8;
            previousTop = section.getBoundingClientRect().top;
            armed = previousTop > triggerLine;

            if (armed) {
                prepare();
            } else {
                showStatic();
            }
        }

        if (armed) {
            prepare();
        } else {
            showStatic();
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', recalculate, { passive: true });
        window.addEventListener('pageshow', recalculate, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
