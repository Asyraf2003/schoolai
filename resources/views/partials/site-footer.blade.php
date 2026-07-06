{{-- SHARED_FOOTER_COMPONENT_FINAL --}}
@php
  $siteFooter = $siteFooter ?? ($footerSection ?? __('home.footer'));
  $siteFooter = is_array($siteFooter) ? $siteFooter : [];
  $isHomeFooter = request()->routeIs('home');

  $normalizeFooterHref = function (mixed $href) use ($isHomeFooter): string {
      if (! is_string($href) || trim($href) === '') {
          return '#';
      }

      $href = trim($href);

      if (str_starts_with($href, '#')) {
          return $isHomeFooter ? $href : route('home') . $href;
      }

      return $href;
  };

  $externalTarget = function (string $href): string {
      return str_starts_with($href, 'http') ? '_blank' : '_self';
  };

  $externalRel = function (string $href): string {
      return str_starts_with($href, 'http') ? 'noopener noreferrer' : '';
  };
@endphp

<!-- SHARED_FOOTER_COMPONENT_FINAL -->
<footer class="site-footer" id="kontak">
  <div class="container site-footer__grid">
    <div class="footer-brand">
      @if (! empty($siteFooter['brand']))
        <a href="{{ $normalizeFooterHref($siteFooter['brand']['href'] ?? '#beranda') }}" class="footer-brand__logo" aria-label="{{ $siteFooter['brand']['name'] ?? __('pages.common.school_name') }}">
          @if (! empty($siteFooter['brand']['image']))
            <img
              src="{{ $siteFooter['brand']['image'] }}"
              alt="{{ $siteFooter['brand']['image_alt'] ?? ($siteFooter['brand']['name'] ?? __('pages.common.school_name')) }}"
              class="footer-brand__logo-image"
              loading="lazy"
              decoding="async"
            />
          @else
            <span class="footer-brand__fallback">{{ $siteFooter['brand']['name'] ?? __('pages.common.school_name') }}</span>
          @endif
        </a>

        @if (! empty($siteFooter['brand']['description']))
          <p class="footer-brand__description">
            {{ $siteFooter['brand']['description'] }}
          </p>
        @endif
      @endif
    </div>

    <div class="footer-channels">
      @if (! empty($siteFooter['channels']))
        <div class="footer-channel-group" aria-label="{{ $siteFooter['channels_title'] ?? __('pages.common.footer.contact_title') }}">
          <strong>{{ $siteFooter['channels_title'] ?? __('pages.common.footer.contact_title') }}</strong>

          <div class="footer-channel-grid">
            @foreach ($siteFooter['channels'] as $channel)
              @php
                $isDisabled = ! empty($channel['disabled']);
                $channelHref = $normalizeFooterHref($channel['href'] ?? '#');
              @endphp

              @if ($isDisabled)
                <span class="footer-channel footer-channel--{{ $channel['icon'] ?? 'link' }} footer-channel--disabled" aria-disabled="true">
              @else
                <a
                  href="{{ $channelHref }}"
                  class="footer-channel footer-channel--{{ $channel['icon'] ?? 'link' }}"
                  aria-label="{{ $channel['label'] ?? '' }}"
                  target="{{ $externalTarget($channelHref) }}"
                  rel="{{ $externalRel($channelHref) }}"
                >
              @endif
                @if (! empty($channel['asset']))
                  <img
                    src="{{ $channel['asset'] }}"
                    alt="{{ $channel['asset_alt'] ?? ($channel['label'] ?? '') }}"
                    class="footer-channel__asset"
                    loading="lazy"
                    decoding="async"
                    fetchpriority="low"
                    width="28"
                    height="28"
                  />
                @else
                  <span class="footer-channel__fallback">{{ substr((string) ($channel['label'] ?? 'LK'), 0, 2) }}</span>
                @endif

                <span class="footer-channel__body">
                  <span class="footer-channel__label">{{ $channel['label'] ?? '' }}</span>
                  <small class="footer-channel__note">{{ $channel['note'] ?? '' }}</small>
                </span>
              @if ($isDisabled)
                </span>
              @else
                </a>
              @endif
            @endforeach
          </div>
        </div>
      @endif
    </div>

    @if (! empty($siteFooter['links']))
      <nav class="footer-links" aria-label="{{ $siteFooter['links_title'] ?? 'Halaman' }}">
        <h4>{{ $siteFooter['links_title'] ?? 'Halaman' }}</h4>
        <ul>
          @foreach ($siteFooter['links'] as $link)
            <li><a href="{{ $normalizeFooterHref($link['href'] ?? '#') }}">{{ $link['label'] ?? '' }}</a></li>
          @endforeach
        </ul>
      </nav>
    @endif

    @if (! empty($siteFooter['gallery_links']))
      <nav class="footer-gallery-links" aria-label="{{ $siteFooter['gallery_links_title'] ?? __('pages.common.nav.galeri') }}">
        <h4>{{ $siteFooter['gallery_links_title'] ?? __('pages.common.nav.galeri') }}</h4>
        <ul>
          @foreach ($siteFooter['gallery_links'] as $link)
            <li><a href="{{ $normalizeFooterHref($link['href'] ?? '#') }}">{{ $link['label'] ?? '' }}</a></li>
          @endforeach
        </ul>
      </nav>
    @endif

    @if (! empty($siteFooter['partners']))
      <div class="footer-partners" aria-labelledby="footer-partners-heading">
        <h4 id="footer-partners-heading">{{ $siteFooter['partners_title'] ?? 'Mitra' }}</h4>

        <div class="footer-partners__grid">
          @foreach ($siteFooter['partners'] as $partner)
            @php($partnerHref = $normalizeFooterHref($partner['href'] ?? '#'))

            <a
              href="{{ $partnerHref }}"
              class="footer-partner-card"
              aria-label="{{ $partner['label'] ?? '' }}"
              target="{{ $externalTarget($partnerHref) }}"
              rel="{{ $externalRel($partnerHref) }}"
            >
              @if (! empty($partner['image']))
                <img
                  src="{{ $partner['image'] }}"
                  alt="{{ $partner['label'] ?? '' }}"
                  class="footer-partner-card__logo"
                  loading="lazy"
                  decoding="async"
                />
              @else
                <span>{{ $partner['label'] ?? '' }}</span>
              @endif
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>

  <div class="site-footer__bottom">
    <p>
      &copy; <span id="currentYear"></span> {{ $siteFooter['copyright'] ?? __('pages.common.footer.rights') }}
    </p>
  </div>
</footer>
<!-- /SHARED_FOOTER_COMPONENT_FINAL -->
