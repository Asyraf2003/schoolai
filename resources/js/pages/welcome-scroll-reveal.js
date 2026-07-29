import './welcome-gallery-heading.js';

/* Directional reveal-on-scroll for the public homepage.
   Uses IntersectionObserver and keeps motion short enough to remain stable while the page is still scrolling. */

(function () {
    'use strict';

    var observer = null;
    var observedElements = new WeakSet();
    var prefersReducedMotion = window.matchMedia
        ? window.matchMedia('(prefers-reduced-motion: reduce)')
        : null;

    var revealRules = [
        { selector: '.hero__content', directions: ['left'], stagger: 0 },
        { selector: '.hero__visual', directions: ['right'], stagger: 0 },
        { selector: '.stats-ribbon', directions: ['bottom'], stagger: 0 },

        { selector: '.visi-misi__head', directions: ['zoom'], stagger: 0 },
        { selector: '.visi-card', directions: ['left'], stagger: 0 },
        { selector: '.misi-panel', directions: ['right'], stagger: 0 },
        { selector: '.misi-list__item', directions: ['right', 'bottom'], stagger: 45 },

        { selector: '.nilai-section__intro', directions: ['left'], stagger: 0 },
        { selector: '.nilai-card', directions: ['left', 'bottom', 'right', 'zoom'], stagger: 45 },

        { selector: '.program-section__head', directions: ['zoom'], stagger: 0 },
        { selector: '.program-spotlight', directions: ['left'], stagger: 0 },
        { selector: '.program-card', directions: ['right', 'bottom', 'left'], stagger: 50 },

        { selector: '.galeri-story-card', directions: ['left', 'right'], stagger: 40 },
        { selector: '.galeri-story__visual', directions: ['right'], stagger: 0 },
        { selector: '.galeri-section__action', directions: ['bottom'], stagger: 0 },

        { selector: '.artikel-section__head', directions: ['left'], stagger: 0 },
        { selector: '.artikel-digest__hero', directions: ['left'], stagger: 0 },
        { selector: '.artikel-digest-card', directions: ['right', 'bottom'], stagger: 50 },
        { selector: '.artikel-section__action', directions: ['bottom'], stagger: 0 },

        { selector: '.site-footer__grid > *', directions: ['left', 'bottom', 'right'], stagger: 50 },
        { selector: '.site-footer__bottom', directions: ['bottom'], stagger: 0 }
    ];

    function revealImmediately(element) {
        element.classList.add('scroll-reveal--visible');

        if (element.classList.contains('reveal')) {
            element.classList.add('is-visible');
        }
    }

    function observeElement(element) {
        if (observedElements.has(element)) return;

        observedElements.add(element);

        if (!observer || (prefersReducedMotion && prefersReducedMotion.matches)) {
            revealImmediately(element);
            return;
        }

        observer.observe(element);
    }

    function decorateElement(element, direction, delay) {
        if (!element || element.nodeType !== 1) return;
        if (element.hasAttribute('data-scroll-reveal')) return;

        element.setAttribute('data-scroll-reveal', direction || 'bottom');
        element.style.setProperty('--scroll-reveal-delay', Math.min(delay || 0, 180) + 'ms');
        observeElement(element);
    }

    function matchingElements(root, selector) {
        var matches = [];

        if (root.nodeType === 1 && root.matches && root.matches(selector)) {
            matches.push(root);
        }

        if (root.querySelectorAll) {
            matches = matches.concat(Array.prototype.slice.call(root.querySelectorAll(selector)));
        }

        return matches;
    }

    function applyRevealRules(root) {
        revealRules.forEach(function (rule) {
            matchingElements(root, rule.selector).forEach(function (element, index) {
                var directions = rule.directions || ['bottom'];
                var direction = directions[index % directions.length];
                var delay = index * (rule.stagger || 0);

                decorateElement(element, direction, delay);
            });
        });
    }

    function initialise() {
        if ('IntersectionObserver' in window) {
            observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;

                    revealImmediately(entry.target);
                    observer.unobserve(entry.target);
                });
            }, {
                threshold: 0.05,
                rootMargin: '0px 0px 12% 0px'
            });
        }

        applyRevealRules(document);

        // The desktop hero visual is injected from a template by welcome.js.
        if ('MutationObserver' in window) {
            var mutationObserver = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1) {
                            applyRevealRules(node);
                        }
                    });
                });
            });

            mutationObserver.observe(document.body, {
                childList: true,
                subtree: true
            });

            window.setTimeout(function () {
                mutationObserver.disconnect();
            }, 2500);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
