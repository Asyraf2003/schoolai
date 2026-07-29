/* Repeatable gallery heading motion. Replays after returning above the section. */
(function () {
    'use strict';

    function initialise() {
        var heading = document.querySelector('[data-gallery-heading]');
        if (!heading) return;

        var reducedMotion = window.matchMedia
            && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        heading.classList.add('gallery-heading-motion--ready');

        if (reducedMotion || !('IntersectionObserver' in window)) {
            heading.classList.add('gallery-heading-motion--static');
            return;
        }

        var lastScrollY = window.scrollY || window.pageYOffset || 0;
        var direction = 'down';

        function recordDirection() {
            var currentScrollY = window.scrollY || window.pageYOffset || 0;
            var delta = currentScrollY - lastScrollY;

            if (Math.abs(delta) <= 2) return;

            direction = delta > 0 ? 'down' : 'up';
            lastScrollY = currentScrollY;
        }

        function prepareForNextEntry() {
            heading.classList.remove(
                'gallery-heading-motion--animated',
                'gallery-heading-motion--static'
            );
        }

        function showStatic() {
            heading.classList.remove('gallery-heading-motion--animated');
            heading.classList.add('gallery-heading-motion--static');
        }

        function replayAnimation() {
            heading.classList.remove(
                'gallery-heading-motion--animated',
                'gallery-heading-motion--static'
            );

            void heading.offsetWidth;
            heading.classList.add('gallery-heading-motion--animated');
        }

        window.addEventListener('scroll', recordDirection, { passive: true });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var enteredFromBelow = entry.boundingClientRect.top >= 0;

                    if (direction === 'down' && enteredFromBelow) {
                        replayAnimation();
                    } else {
                        showStatic();
                    }

                    return;
                }

                var isBelowViewport = entry.boundingClientRect.top >= window.innerHeight;

                if (isBelowViewport) {
                    prepareForNextEntry();
                }
            });
        }, {
            threshold: 0.14,
            rootMargin: '0px 0px -8% 0px'
        });

        var initialRect = heading.getBoundingClientRect();

        if (initialRect.top < window.innerHeight && initialRect.bottom > 0) {
            showStatic();
        } else if (initialRect.bottom <= 0) {
            showStatic();
        } else {
            prepareForNextEntry();
        }

        observer.observe(heading);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
