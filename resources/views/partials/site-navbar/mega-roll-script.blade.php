<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  (function () {
    var root = document.querySelector('.nav-shell');
    if (!root || root.dataset.navRollBooted === 'true') return;
    root.dataset.navRollBooted = 'true';
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var lastPlayed = new WeakMap();
    var controlSelector = '.nav-link, .nav-mega__link, .navbar__cta';
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
      layer.className = 'nav-roll__layer nav-roll__layer--' + modifier;
      segmentsFor(text, locale, keepWholeWord).forEach(function (segment) {
        var character = document.createElement('span');
        character.className = 'nav-roll__char';
        character.textContent = segment;
        layer.appendChild(character);
      });
      return layer;
    }
    function enhanceLabel(label, locale, keepWholeWord) {
      if (label.dataset.navRollEnhanced === 'true') return;
      var text = (label.textContent || '').trim();
      if (!text) return;
      var viewport = document.createElement('span');
      viewport.className = 'nav-roll__viewport';
      viewport.setAttribute('aria-hidden', 'true');
      viewport.appendChild(createLayer(text, locale, keepWholeWord, 'base'));
      viewport.appendChild(createLayer(text, locale, keepWholeWord, 'clone'));
      var accessible = document.createElement('span');
      accessible.className = 'nav-roll__accessible';
      accessible.textContent = text;
      label.textContent = '';
      label.classList.add('nav-roll');
      if (keepWholeWord) label.classList.add('nav-roll--arabic');
      label.appendChild(viewport);
      label.appendChild(accessible);
      label.dataset.navRollEnhanced = 'true';
    }
    function cancelAnimations(label) {
      label.querySelectorAll('.nav-roll__char').forEach(function (character) {
        character.getAnimations().forEach(function (animation) { animation.cancel(); });
      });
    }
    function playArabicSweep(label, delay, reverse) {
      var base = label.querySelector('.nav-roll__layer--base .nav-roll__char');
      var clone = label.querySelector('.nav-roll__layer--clone .nav-roll__char');
      if (!base || !clone) return;
      var start = reverse ? 'inset(0 100% 0 0)' : 'inset(0 0 0 100%)';
      var end = reverse ? 'inset(0 0 0 100%)' : 'inset(0 100% 0 0)';
      var options = {
        duration: 680,
        delay: delay,
        easing: 'cubic-bezier(0.22, 1, 0.36, 1)'
      };
      base.animate([
        { opacity: 1 },
        { opacity: 0.62, offset: 0.46 },
        { opacity: 1 }
      ], options);
      clone.animate([
        { opacity: 0, clipPath: start, webkitClipPath: start },
        { opacity: 1, clipPath: 'inset(0)', webkitClipPath: 'inset(0)', offset: 0.46 },
        { opacity: 0, clipPath: end, webkitClipPath: end }
      ], options);
    }
    function playRoll(label, delay, reverse) {
      if (!label || reducedMotion.matches || typeof label.animate !== 'function') return;
      var now = performance.now();
      var previous = lastPlayed.get(label) || 0;
      if (!delay && now - previous < 180) return;
      lastPlayed.set(label, now);
      cancelAnimations(label);
      if (label.classList.contains('nav-roll--arabic')) {
        playArabicSweep(label, delay, reverse);
        return;
      }
      var baseCharacters = label.querySelectorAll('.nav-roll__layer--base .nav-roll__char');
      var cloneCharacters = label.querySelectorAll('.nav-roll__layer--clone .nav-roll__char');
      var distance = reverse ? '-105%' : '105%';
      var baseRotation = reverse ? '90deg' : '-90deg';
      var cloneDistance = reverse ? '105%' : '-105%';
      var cloneRotation = reverse ? '-90deg' : '90deg';
      baseCharacters.forEach(function (character, index) {
        character.animate([
          { opacity: 1, transform: 'translateY(0) rotateX(0deg)' },
          { opacity: 0, transform: 'translateY(' + distance + ') rotateX(' + baseRotation + ')' }
        ], {
          duration: 520,
          delay: delay + (index * 22),
          easing: 'cubic-bezier(0.22, 1, 0.36, 1)'
        });
      });
      cloneCharacters.forEach(function (character, index) {
        character.animate([
          { opacity: 0, transform: 'translateY(' + cloneDistance + ') rotateX(' + cloneRotation + ')' },
          { opacity: 1, transform: 'translateY(0) rotateX(0deg)' }
        ], {
          duration: 520,
          delay: delay + (index * 22),
          easing: 'cubic-bezier(0.22, 1, 0.36, 1)'
        });
      });
    }
    function labelsInside(control) {
      return control ? control.querySelectorAll('[data-nav-roll]') : [];
    }
    function playControl(control, reverse) {
      labelsInside(control).forEach(function (label) { playRoll(label, 0, reverse); });
    }
    function relatedStayedInside(control, relatedTarget) {
      return relatedTarget instanceof Node && control.contains(relatedTarget);
    }
    root.addEventListener('pointerover', function (event) {
      var control = event.target.closest(controlSelector);
      if (!control || relatedStayedInside(control, event.relatedTarget)) return;
      playControl(control, false);
    });
    root.addEventListener('pointerout', function (event) {
      var control = event.target.closest(controlSelector);
      if (!control || relatedStayedInside(control, event.relatedTarget)) return;
      playControl(control, true);
    });
    root.addEventListener('focusin', function (event) {
      playControl(event.target.closest(controlSelector), false);
    });
    root.addEventListener('focusout', function (event) {
      var control = event.target.closest(controlSelector);
      if (!control || relatedStayedInside(control, event.relatedTarget)) return;
      playControl(control, true);
    });
    root.addEventListener('click', function (event) {
      playControl(event.target.closest(controlSelector), false);
    });
    function playSequence(labels, initialDelay, step) {
      labels.forEach(function (label, index) {
        playRoll(label, initialDelay + (index * step), false);
      });
    }
    var observer = new MutationObserver(function (records) {
      records.forEach(function (record) {
        var target = record.target;
        var oldClasses = record.oldValue || '';
        var becameActive = target.matches('[data-mobile-navigation-layer]')
          && target.classList.contains('active')
          && !oldClasses.split(/\s+/).includes('active');
        var openedMega = target.matches('[data-nav-mega]')
          && target.classList.contains('is-open')
          && !oldClasses.split(/\s+/).includes('is-open');
        if (becameActive) {
          playSequence(target.querySelectorAll('[data-nav-roll="main"]'), 420, 54);
        }
        if (openedMega) {
          playControl(target.querySelector('[data-nav-mega-toggle]'), false);
          playSequence(target.querySelectorAll('[data-nav-mega-panel] [data-nav-roll="sub"]'), 90, 46);
        }
      });
    });
    function boot() {
      var locale = document.documentElement.lang || 'id';
      var keepWholeWord = document.documentElement.dir === 'rtl'
        || locale.toLowerCase().indexOf('ar') === 0;
      root.querySelectorAll('[data-nav-roll]').forEach(function (label) {
        enhanceLabel(label, locale, keepWholeWord);
      });
      observer.observe(root, {
        attributes: true,
        attributeFilter: ['class'],
        attributeOldValue: true,
        subtree: true
      });
    }
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
      boot();
    }
  })();
</script>