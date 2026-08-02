function graphemes(text, locale) {
    if (typeof Intl !== 'undefined' && typeof Intl.Segmenter === 'function') {
        return Array.from(
            new Intl.Segmenter(locale, { granularity: 'grapheme' }).segment(text),
            function (entry) { return entry.segment; }
        );
    }

    return Array.from(text);
}

function appendLatinWords(layer, text, locale) {
    text.split(/(\s+)/u).forEach(function (token) {
        if (/^\s+$/u.test(token)) {
            layer.appendChild(document.createTextNode(token));
            return;
        }

        var word = document.createElement('span');
        word.className = 'hero-title-glow__word';
        graphemes(token, locale).forEach(function (segment) {
            var character = document.createElement('span');
            character.className = 'hero-title-glow__char';
            character.textContent = segment;
            word.appendChild(character);
        });
        layer.appendChild(word);
    });
}

function enhanceTitle(title, locale, isRtl) {
    if (title.dataset.heroTitleGlowEnhanced === 'true') return;
    var source = title.querySelector('[data-hero-title-base]');
    var text = source ? source.textContent : '';
    if (!text || !text.trim()) return;

    var overlay = document.createElement('span');
    overlay.className = 'hero-title-glow__overlay ' +
        (isRtl ? 'hero-title-glow__overlay--run' : 'hero-title-glow__overlay--latin');
    overlay.setAttribute('aria-hidden', 'true');

    if (isRtl) {
        overlay.textContent = text;
    } else {
        appendLatinWords(overlay, text, locale);
    }

    title.appendChild(overlay);
    title.dataset.heroTitleGlowEnhanced = 'true';
}

export function initHeroTitleGlow(root) {
    if (root.dataset.heroTitleGlowBooted === 'true') return;
    root.dataset.heroTitleGlowBooted = 'true';

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var locale = document.documentElement.lang || 'id';
    var isRtl = document.documentElement.dir === 'rtl';
    var generations = new WeakMap();
    var activationTimer = null;

    root.querySelectorAll('[data-hero-title-glow]').forEach(function (title) {
        enhanceTitle(title, locale, isRtl);
    });

    function cancelAnimations(title) {
        title.querySelectorAll('.hero-title-glow__overlay, .hero-title-glow__char')
            .forEach(function (node) {
                node.getAnimations().forEach(function (animation) { animation.cancel(); });
            });
    }

    function play(title) {
        var overlay = title && title.querySelector('.hero-title-glow__overlay');
        if (!overlay || reducedMotion.matches || typeof overlay.animate !== 'function') return;

        var generation = (generations.get(title) || 0) + 1;
        generations.set(title, generation);
        cancelAnimations(title);
        overlay.style.opacity = '1';

        if (overlay.classList.contains('hero-title-glow__overlay--run')) {
            var start = isRtl ? '-80% 50%' : '180% 50%';
            var end = isRtl ? '180% 50%' : '-80% 50%';
            overlay.animate([
                { opacity: 0, backgroundPosition: start, filter: 'blur(4px)' },
                { opacity: 0.95, backgroundPosition: '50% 50%', filter: 'blur(0)', offset: 0.52 },
                { opacity: 0, backgroundPosition: end, filter: 'blur(3px)' }
            ], { duration: 980, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' });
            return;
        }

        overlay.querySelectorAll('.hero-title-glow__char').forEach(function (character, index) {
            character.animate([
                { opacity: 0, transform: 'translateY(7px)', filter: 'blur(3px)' },
                { opacity: 1, transform: 'translateY(0)', filter: 'blur(0)', offset: 0.48 },
                { opacity: 0, transform: 'translateY(-2px)', filter: 'blur(2px)' }
            ], {
                duration: 620,
                delay: index * 24,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)'
            });
        });
    }

    function playActivatedTitle(title) {
        if (activationTimer !== null) window.clearTimeout(activationTimer);
        activationTimer = window.setTimeout(function () {
            activationTimer = null;
            play(title);
        }, 140);
    }

    function settle(title) {
        var overlay = title && title.querySelector('.hero-title-glow__overlay');
        if (!overlay || reducedMotion.matches || typeof overlay.animate !== 'function') return;
        var generation = (generations.get(title) || 0) + 1;
        generations.set(title, generation);
        var fade = overlay.animate(
            [{ opacity: getComputedStyle(overlay).opacity }, { opacity: 0 }],
            { duration: 180, easing: 'ease-out', fill: 'forwards' }
        );
        fade.finished.then(function () {
            if (generations.get(title) !== generation) return;
            cancelAnimations(title);
            overlay.style.opacity = '';
        }).catch(function () {});
    }

    function titleFrom(target) {
        return target instanceof Element ? target.closest('[data-hero-title-glow]') : null;
    }

    root.addEventListener('pointerover', function (event) {
        if (event.pointerType === 'touch') return;
        var title = titleFrom(event.target);
        if (!title || (event.relatedTarget instanceof Node && title.contains(event.relatedTarget))) return;
        play(title);
    });
    root.addEventListener('pointerout', function (event) {
        var title = titleFrom(event.target);
        if (!title || (event.relatedTarget instanceof Node && title.contains(event.relatedTarget))) return;
        settle(title);
    });
    root.addEventListener('focusin', function (event) { play(titleFrom(event.target)); });
    root.addEventListener('focusout', function (event) { settle(titleFrom(event.target)); });
    root.addEventListener('hero:slide-active', function (event) {
        playActivatedTitle(event.detail && event.detail.slide
            ? event.detail.slide.querySelector('[data-hero-title-glow]')
            : null);
    });
}
