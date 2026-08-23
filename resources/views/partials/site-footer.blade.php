<footer class="site-footer{{ $isHomeFooter ? ' site-footer--home-story' : '' }}" id="kontak">
  <div class="container site-footer__grid">
    <div class="footer-brand">
      @if (! empty($siteFooter['brand']))
        <a href="{{ $siteFooter['brand']['resolved_href'] }}" class="footer-brand__logo" aria-label="{{ $siteFooter['brand']['name'] ?? __('pages.common.school_name') }}">
          @if (! empty($siteFooter['brand']['image']))
            <img
              src="{{ $siteFooter['brand']['image'] }}"
              alt="{{ $siteFooter['brand']['image_alt'] ?? ($siteFooter['brand']['name'] ?? __('pages.common.school_name')) }}"
              width="192"
              height="192"
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
              @if ($channel['is_disabled'])
                <span class="footer-channel footer-channel--{{ $channel['icon'] ?? 'link' }} footer-channel--disabled" aria-disabled="true" aria-label="{{ $channel['label'] ?? '' }}">
              @else
                <a
                  href="{{ $channel['resolved_href'] }}"
                  class="footer-channel footer-channel--{{ $channel['icon'] ?? 'link' }}"
                  aria-label="{{ $channel['label'] ?? '' }}"
                  target="{{ $channel['external_target'] }}"
                  rel="{{ $channel['external_rel'] }}"
                >
              @endif
                @if (! empty($channel['asset']))
                  <img
                    src="{{ $channel['asset'] }}"
                    alt=""
                    aria-hidden="true"
                    class="footer-channel__asset"
                    loading="lazy"
                    decoding="async"
                    fetchpriority="low"
                    width="28"
                    height="28"
                  />
                @else
                  <span class="footer-channel__fallback" aria-hidden="true">{{ $channel['fallback'] }}</span>
                @endif
              @if ($channel['is_disabled'])
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
      <nav class="footer-links" aria-label="{{ $siteFooter['links_title'] ?? __('pages.common.footer.links_title') }}">
        <strong class="footer-links__title">{{ $siteFooter['links_title'] ?? __('pages.common.footer.links_title') }}</strong>
        <ul>
          @foreach ($siteFooter['links'] as $link)
            <li><a href="{{ $link['resolved_href'] }}">{{ $link['label'] ?? '' }}</a></li>
          @endforeach
        </ul>
      </nav>
    @endif

    @if (! empty($siteFooter['gallery_links']))
      <nav class="footer-gallery-links" aria-label="{{ $siteFooter['gallery_links_title'] ?? __('pages.common.nav.galeri') }}">
        <strong class="footer-gallery-links__title">{{ $siteFooter['gallery_links_title'] ?? __('pages.common.nav.galeri') }}</strong>
        <ul>
          @foreach ($siteFooter['gallery_links'] as $link)
            <li><a href="{{ $link['resolved_href'] }}">{{ $link['label'] ?? '' }}</a></li>
          @endforeach
        </ul>
      </nav>
    @endif

    @if (! empty($siteFooter['partners']))
      <div class="footer-partners">
        <strong class="footer-partners__title">{{ $siteFooter['partners_title'] ?? 'Mitra' }}</strong>

        <div class="footer-partners__grid">
          @foreach ($siteFooter['partners'] as $partner)
            <a
              href="{{ $partner['resolved_href'] }}"
              class="footer-partner-card"
              aria-label="{{ $partner['label'] ?? '' }}"
              target="{{ $partner['external_target'] }}"
              rel="{{ $partner['external_rel'] }}"
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
