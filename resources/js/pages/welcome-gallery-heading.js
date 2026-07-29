/* Replay the gallery heading whenever the user returns above it and scrolls down again. */
(function () {
    'use strict';

    function initialise() {
        var heading = document.querySelector('[data-gallery-heading]');
        if (!heading) return;

        var reducedMotion = window.matchMedia
            && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        heading.classList.add('gallery-heading-motion--ready');

        if (reducedMotion) {
            heading.classList.add('gallery-heading-motion--static');
            return;
        }

        var lastScrollY = window.scrollY || window.pageYOffset || 0;
        var previousTop = heading.getBoundingClientRect().top;
        var armed = previousTop >= window.innerHeight;
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
            var rect = heading.getBoundingClientRect();
            var triggerLine = window.innerHeight * 0.82;

            if (Math.abs(delta) > 1) {
                if (delta < 0) {
                    showStatic();

                    if (rect.top >= window.innerHeight) {
                        armed = true;
                        prepare();
                    }
                } else if (
                    armed
                    && previousTop > triggerLine
                    && rect.top <= triggerLine
                ) {
                    replay();
                    armed = false;
                }

                lastScrollY = currentScrollY;
            }

            previousTop = rect.top;
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(update);
        }

        if (previousTop >= window.innerHeight) {
            prepare();
        } else {
            showStatic();
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', function () {
            previousTop = heading.getBoundingClientRect().top;
        }, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
