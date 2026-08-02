function segmentsFor(text, locale, keepWholeWord) {
    if (keepWholeWord) return [text];
    if (typeof Intl !== 'undefined' && typeof Intl.Segmenter === 'function') {
        return Array.from(
            new Intl.Segmenter(locale, { granularity: 'grapheme' }).segment(text),
            function (entry) { return entry.segment; }
        );
    }
    return Array.from(text);
}

function createLayer(text, locale, keepWholeWord, modifier) {
    var layer = document.createElement('span');
    layer.className = 'hero-roll__layer hero-roll__layer--' + modifier;
    segmentsFor(text, locale, keepWholeWord).forEach(function (segment) {
        var character = document.createElement('span');
        character.className = 'hero-roll__char';
        character.textContent = segment;
        layer.appendChild(character);
    });
    return layer;
}

function enhanceLabel(label, locale, keepWholeWord) {
    if (label.dataset.heroRollEnhanced === 'true') return;
    var text = (label.textContent || '').trim();
    if (!text) return;

    var viewport = document.createElement('span');
    viewport.className = 'hero-roll__viewport';
    viewport.setAttribute('aria-hidden', 'true');
    viewport.appendChild(createLayer(text, locale, keepWholeWord, 'base'));
    viewport.appendChild(createLayer(text, locale, keepWholeWord, 'incoming'));

    var accessible = document.createElement('span');
    accessible.className = 'hero-roll__accessible';
    accessible.textContent = text;
    label.textContent = '';
    label.classList.add('hero-roll');
    label.appendChild(viewport);
    label.appendChild(accessible);
    label.dataset.heroRollEnhanced = 'true';
}

export function initHeroPpdbRoll(root) {
    if (root.dataset.heroPpdbRollBooted === 'true') return;
    root.dataset.heroPpdbRollBooted = 'true';
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var locale = document.documentElement.lang || 'id';
    var keepWholeWord = document.documentElement.dir === 'rtl';

    root.querySelectorAll('[data-hero-roll-label]').forEach(function (label) {
        enhanceLabel(label, locale, keepWholeWord);
    });

    function play(control, reverse) {
        var label = control && control.querySelector('[data-hero-roll-label]');
        if (!label || reducedMotion.matches || typeof label.animate !== 'function') return;
        label.querySelectorAll('.hero-roll__char').forEach(function (character) {
            character.getAnimations().forEach(function (animation) { animation.cancel(); });
        });

        var base = label.querySelectorAll('.hero-roll__layer--base .hero-roll__char');
        var incoming = label.querySelectorAll('.hero-roll__layer--incoming .hero-roll__char');
        var outDistance = reverse ? '-105%' : '105%';
        var outRotation = reverse ? '90deg' : '-90deg';
        var inDistance = reverse ? '105%' : '-105%';
        var inRotation = reverse ? '-90deg' : '90deg';

        base.forEach(function (character, index) {
            character.animate([
                { opacity: 1, transform: 'translateY(0) rotateX(0deg)' },
                { opacity: 0, transform: 'translateY(' + outDistance + ') rotateX(' + outRotation + ')' }
            ], { duration: 520, delay: index * 22, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' });
        });
        incoming.forEach(function (character, index) {
            character.animate([
                { opacity: 0, transform: 'translateY(' + inDistance + ') rotateX(' + inRotation + ')' },
                { opacity: 1, transform: 'translateY(0) rotateX(0deg)' }
            ], { duration: 520, delay: index * 22, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' });
        });
    }

    function controlFrom(target) {
        return target instanceof Element ? target.closest('[data-hero-ppdb-cta]') : null;
    }

    root.addEventListener('pointerover', function (event) {
        if (event.pointerType === 'touch') return;
        var control = controlFrom(event.target);
        if (!control || (event.relatedTarget instanceof Node && control.contains(event.relatedTarget))) return;
        play(control, false);
    });
    root.addEventListener('pointerout', function (event) {
        var control = controlFrom(event.target);
        if (!control || (event.relatedTarget instanceof Node && control.contains(event.relatedTarget))) return;
        play(control, true);
    });
    root.addEventListener('focusin', function (event) { play(controlFrom(event.target), false); });
    root.addEventListener('focusout', function (event) { play(controlFrom(event.target), true); });
}
