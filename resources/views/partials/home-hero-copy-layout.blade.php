@once
  @vite([
    'resources/css/pages/welcome-mega-menu.css',
    'resources/css/pages/welcome-hero-motion.css',
    'resources/css/pages/welcome-hero-visual.css',
    'resources/css/pages/welcome-hero-youtube.css',
    'resources/js/pages/welcome-hero-youtube.js',
  ])

  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    .home-page .hero-cinema__content { padding-block-end: clamp(108px, 12vh, 142px); }
    .home-page .hero-cinema__copy { width: min(620px, 41vw); max-width: 620px; }
    .home-page .hero-cinema__eyebrow { font-size: clamp(0.62rem, 0.72vw, 0.74rem); letter-spacing: 0.17em; }
    .home-page .hero-cinema__eyebrow::before { width: 32px; }
    .home-page .hero-cinema__title { max-width: 22ch; margin-block-start: clamp(10px, 1.4vh, 16px); font-size: clamp(2.2rem, 3vw, 3.9rem); line-height: 0.96; letter-spacing: -0.045em; text-wrap: balance; }
    .home-page .hero-cinema__description { max-width: 48ch; margin-block-start: clamp(14px, 1.8vh, 20px); font-size: clamp(0.84rem, 0.92vw, 0.98rem); line-height: 1.5; }
    .home-page .hero-cinema__cta { min-height: 44px; margin-block-start: clamp(14px, 2vh, 22px); padding-block: 10px; font-size: 0.8rem; }

    .home-page .hero-cinema__eyebrow,
    .home-page .hero-cinema__description,
    .home-page .hero-cinema__cta {
      width: fit-content;
      max-width: min(100%, 52ch);
      padding: 9px 13px;
      border: 1px solid rgb(255 255 255 / 0.14);
      border-radius: 12px;
      color: #fff;
      background: rgb(3 18 16 / 0.48);
      -webkit-backdrop-filter: blur(14px) saturate(120%);
      backdrop-filter: blur(14px) saturate(120%);
      box-shadow: 0 10px 34px rgb(0 0 0 / 0.28);
      font-weight: 820;
      text-shadow: 0 2px 12px rgb(0 0 0 / 0.5);
    }

    .home-page .hero-cinema__description { padding: 11px 14px; }
    .home-page .hero-cinema__cta { padding-inline: 14px; border-color: rgb(247 178 75 / 0.42); }
    .home-page .hero-cinema__cta::after { inset-inline: 14px; }

    /* YouTube is presentation media here, not an interactive player. */
    .home-page .hero-cinema__youtube-frame iframe {
      pointer-events: none !important;
      user-select: none;
    }
    .home-page [data-hero-playback],
    .home-page .hero-cinema__playback { display: none !important; }

    .home-page .hero-cinema__arrow {
      inset-block-start: auto;
      width: clamp(92px, 7.4vw, 128px);
      height: clamp(52px, 4.5vw, 72px);
      padding: 0;
      border: 0;
      border-radius: 0;
      color: #ffb20b;
      background: transparent;
      -webkit-backdrop-filter: none;
      backdrop-filter: none;
      filter: drop-shadow(0 8px 16px rgb(0 0 0 / 0.36));
      transform: none;
    }
    .home-page .hero-cinema__arrow--previous {
      inset-inline-start: auto;
      inset-inline-end: clamp(118px, 12vw, 205px);
      inset-block-end: clamp(150px, 18vh, 215px);
    }
    .home-page .hero-cinema__arrow--next {
      inset-inline-end: clamp(54px, 6vw, 105px);
      inset-block-end: clamp(82px, 10vh, 126px);
    }
    .home-page .hero-cinema__arrow:hover,
    .home-page .hero-cinema__arrow:focus-visible { color: #ffd166; background: transparent; transform: scale(1.08); }
    .home-page .hero-cinema__arrow:focus-visible { outline: 3px solid rgb(255 255 255 / 0.92); outline-offset: 5px; }
    .home-page .hero-cinema__arrow svg { width: 100%; height: 100%; overflow: visible; fill: currentColor; }
    .home-page .hero-cinema__arrow svg path:nth-child(2) { opacity: 0.82; }
    .home-page .hero-cinema__arrow svg path:nth-child(3) { opacity: 0.64; }

    @media (max-width: 1180px) {
      .home-page .hero-cinema__content { padding-block-end: clamp(104px, 12vh, 132px); }
      .home-page .hero-cinema__copy { width: min(560px, 58vw); max-width: 560px; }
      .home-page .hero-cinema__title { max-width: 20ch; font-size: clamp(2.15rem, 4.7vw, 3.35rem); }
      .home-page .hero-cinema__arrow--previous { inset-inline-end: clamp(104px, 11vw, 150px); inset-block-end: clamp(142px, 17vh, 190px); }
      .home-page .hero-cinema__arrow--next { inset-inline-end: clamp(38px, 5vw, 74px); inset-block-end: clamp(78px, 9vh, 112px); }
    }

    @media (max-width: 767px) {
      .home-page .hero-cinema__content { align-items: flex-end; padding-block-start: calc(var(--hero-nav-height) + 28px); padding-block-end: 108px; }
      .home-page .hero-cinema__copy { width: min(84vw, 340px); max-width: min(84vw, 340px); }
      .home-page .hero-cinema__eyebrow { font-size: 0.56rem; letter-spacing: 0.13em; }
      .home-page .hero-cinema__eyebrow::before { width: 22px; }
      .home-page .hero-cinema__title { max-width: 18ch; margin-block-start: 10px; font-size: clamp(1.9rem, 8.4vw, 2.55rem); line-height: 0.98; letter-spacing: -0.04em; }
      .home-page .hero-cinema__description { max-width: 34ch; margin-block-start: 12px; font-size: clamp(0.74rem, 3vw, 0.84rem); line-height: 1.45; }
      .home-page .hero-cinema__cta { min-height: 38px; margin-block-start: 12px; padding-block: 8px; font-size: 0.72rem; }
      .home-page .hero-cinema__cta svg { width: 15px; height: 15px; }

      .home-page .hero-cinema__arrow {
        display: grid;
        width: 76px;
        height: 44px;
        border: 0;
        background: transparent;
        box-shadow: none;
      }
      .home-page .hero-cinema__arrow--previous { inset-inline-start: auto; inset-inline-end: 76px; inset-block-end: 150px; }
      .home-page .hero-cinema__arrow--next { inset-inline-end: 20px; inset-block-end: 96px; }
      .home-page .hero-cinema__arrow svg { width: 100%; height: 100%; }
    }

    @media (max-width: 440px) {
      .home-page .hero-cinema__content { padding-block-end: 102px; }
      .home-page .hero-cinema__copy { width: min(82vw, 315px); max-width: min(82vw, 315px); }
      .home-page .hero-cinema__title { max-width: 17ch; font-size: clamp(1.72rem, 7.8vw, 2.18rem); }
      .home-page .hero-cinema__description { max-width: 32ch; font-size: 0.74rem; -webkit-line-clamp: 3; }
      .home-page .hero-cinema__arrow { width: 68px; height: 40px; }
      .home-page .hero-cinema__arrow--previous { inset-inline-end: 70px; inset-block-end: 144px; }
      .home-page .hero-cinema__arrow--next { inset-inline-end: 18px; inset-block-end: 92px; }
    }
  </style>

  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (function () {
      function youtubeUrl(value) {
        if (!value) return null;

        try {
          var url = new URL(value, window.location.origin);
          var host = url.hostname.toLowerCase();

          if (
            host !== 'www.youtube.com' &&
            host !== 'youtube.com' &&
            host !== 'www.youtube-nocookie.com' &&
            host !== 'youtube-nocookie.com'
          ) {
            return null;
          }

          if (!/^\/embed\/[A-Za-z0-9_-]{6,32}\/?$/.test(url.pathname)) return null;

          url.searchParams.set('autoplay', '1');
          url.searchParams.set('mute', '1');
          url.searchParams.set('controls', '0');
          url.searchParams.set('disablekb', '1');
          url.searchParams.set('fs', '0');
          url.searchParams.set('iv_load_policy', '3');
          url.searchParams.set('playsinline', '1');
          url.searchParams.set('rel', '0');
          url.searchParams.set('modestbranding', '1');
          url.searchParams.set('enablejsapi', '1');
          url.searchParams.set('origin', window.location.origin);
          url.searchParams.set('loop', '0');
          url.searchParams.delete('playlist');

          return url.toString();
        } catch (error) {
          return null;
        }
      }

      function prepareVideo(video) {
        var source = video.querySelector('source[data-src], source[src]');
        if (!source) return;

        var attribute = source.hasAttribute('data-src') ? 'data-src' : 'src';
        var normalized = youtubeUrl(source.getAttribute(attribute));
        if (!normalized) return;

        source.setAttribute(attribute, normalized);
        video.removeAttribute('controls');
        video.removeAttribute('loop');
        video.muted = true;
        video.setAttribute('muted', '');
      }

      function prepareFrame(frame) {
        ['data-src', 'src'].forEach(function (attribute) {
          if (!frame.hasAttribute(attribute)) return;

          var current = frame.getAttribute(attribute);
          var normalized = youtubeUrl(current);
          if (normalized && normalized !== current) {
            frame.setAttribute(attribute, normalized);
          }
        });

        frame.setAttribute('loading', 'eager');
        frame.setAttribute('tabindex', '-1');
        frame.setAttribute('aria-hidden', 'true');
        frame.setAttribute('allow', 'autoplay; encrypted-media');
        frame.style.setProperty('pointer-events', 'none', 'important');
      }

      function scan(root) {
        if (!root || !root.querySelectorAll) return;

        if (root.matches && root.matches('[data-hero-video]')) prepareVideo(root);
        if (root.matches && root.matches('[data-hero-youtube]')) prepareFrame(root);

        root.querySelectorAll('[data-hero-video]').forEach(prepareVideo);
        root.querySelectorAll('[data-hero-youtube]').forEach(prepareFrame);
        root.querySelectorAll('[data-hero-playback], .hero-cinema__playback').forEach(function (control) {
          control.remove();
        });
      }

      scan(document);

      var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1) scan(node);
          });
        });
      });

      observer.observe(document.documentElement, { childList: true, subtree: true });

      document.addEventListener('DOMContentLoaded', function () {
        scan(document);
        window.requestAnimationFrame(function () { scan(document); });
        window.setTimeout(function () {
          scan(document);
          observer.disconnect();
        }, 2000);
      }, { once: true });
    })();
  </script>
@endonce
