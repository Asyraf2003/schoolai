<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  (function () {
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

      segmentsFor(text, locale, keepWholeWord).forEach(function (segment, index) {
        var character = document.createElement('span');
        character.className = 'nav-roll__char';
        character.style.setProperty('--roll-index', index);
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
      label.appendChild(viewport);
      label.appendChild(accessible);
      label.dataset.navRollEnhanced = 'true';
    }

    function bootMegaRoll() {
      var root = document.documentElement;
      var locale = root.lang || 'id';
      var keepWholeWord = root.dir === 'rtl' || locale.toLowerCase().indexOf('ar') === 0;

      document.querySelectorAll('[data-nav-roll]').forEach(function (label) {
        enhanceLabel(label, locale, keepWholeWord);
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', bootMegaRoll, { once: true });
    } else {
      bootMegaRoll();
    }
  })();
</script>
