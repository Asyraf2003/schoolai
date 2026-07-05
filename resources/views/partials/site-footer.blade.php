{{-- PUBLIC_FOOTER_DUMMY_FINAL --}}
<footer class="public-footer">
  <div class="container public-footer__grid">
    <div>
      <p class="public-footer__brand">{{ __('pages.common.school_name') }}</p>
      <p class="public-footer__text">{{ __('pages.common.footer.description') }}</p>
    </div>

    <div>
      <p class="public-footer__title">{{ __('pages.common.footer.contact_title') }}</p>
      <p><strong>{{ __('pages.common.footer.address_label') }}:</strong> {{ __('pages.common.footer.address') }}</p>
      <p><strong>{{ __('pages.common.footer.phone_label') }}:</strong> {{ __('pages.common.footer.phone') }}</p>
      <p><strong>{{ __('pages.common.footer.email_label') }}:</strong> {{ __('pages.common.footer.email') }}</p>
    </div>

    <div>
      <p class="public-footer__title">{{ __('pages.common.footer.social_title') }}</p>
      <p>{{ __('pages.common.footer.instagram') }}</p>
      <a class="link-arrow" href="{{ route('home') }}#kontak">{{ __('pages.common.nav.contact') }} →</a>
    </div>
  </div>

  <div class="container public-footer__bottom">
    <span>© <span id="currentYear"></span> {{ __('pages.common.school_name') }}.</span>
    <span>{{ __('pages.common.footer.rights') }}</span>
  </div>
</footer>
