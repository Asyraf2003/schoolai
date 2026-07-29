/* One-shot gallery heading motion. It animates only when first entered while scrolling down. */
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

        var initialRect = heading.getBoundingClientRect();
        if (initialRect.top < 0 || initialRect.bottom <= 0) {
            heading.classList.add('gallery-heading-motion--static');
            return;
        }

        var lastScrollY = window.scrollY || window.pageYOffset || 0;
        var direction = 'down';
        var hasScrolled = false;

        function recordDirection() {
            var currentScrollY = window.scrollY || window.pageYOffset || 0;
            var delta = currentScrollY - lastScrollY;

            if (Math.abs(delta) <= 2) return;

            direction = delta > 0 ? 'down' : 'up';
            hasScrolled = true;
            lastScrollY = currentScrollY;
        }

        window.addEventListener('scroll', recordDirection, { passive: true });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                var shouldAnimate = hasScrolled
                    && direction === 'down'
                    && entry.boundingClientRect.top >= 0;

                heading.classList.add(
                    shouldAnimate
                        ? 'gallery-heading-motion--animated'
                        : 'gallery-heading-motion--static'
                );

                observer.unobserve(heading);
                window.removeEventListener('scroll', recordDirection);
            });
        }, {
            threshold: 0.16,
            rootMargin: '0px 0px -10% 0px'
        });

        observer.observe(heading);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
