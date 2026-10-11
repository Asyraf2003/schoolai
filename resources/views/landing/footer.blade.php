@php
    $footerChannels = collect(__('home.footer.channels'))->keyBy('icon');
    $footerPartners = __('home.footer.partners');
    $footerAssets = config('media.static.footer');
    $partnerAssets = $footerAssets['partners'];
    $socialIcons = ['whatsapp', 'maps', 'instagram', 'email', 'facebook'];
    $iconAssets = ['whatsapp' => 'whatsapp', 'maps' => 'maps', 'instagram' => 'instagram',
        'email' => 'gmail', 'facebook' => 'facebook'];
@endphp
<footer class="site-footer-v2" id="kontak" data-footer-v2>
    <div class="site-footer-v2__white">
        <div class="site-footer-v2__visual" data-footer-visual aria-hidden="true">
            <img data-footer-visual-image alt="" loading="lazy" decoding="async">
        </div>
        <div class="site-footer-v2__top-space" aria-hidden="true"></div>
        <div class="site-footer-v2__content">
            <div class="site-footer-v2__about">
                <p class="site-footer-v2__description">{{ __('home.footer.brand.description') }}</p>
            </div>
            <nav class="site-footer-v2__social" aria-label="{{ __('footer_v2.social_label') }}">
                @foreach ($socialIcons as $key)
                    @php($channel = $footerChannels->get($key))
                    <a href="{{ $channel['href'] }}" data-footer-preview="{{ $key }}"
                       data-footer-preview-src="{{ $footerAssets[$iconAssets[$key]] }}"
                       @if (str_starts_with($channel['href'], 'https://')) target="_blank" rel="noopener noreferrer" @endif>
                        {{ $key === 'maps' ? 'Maps' : ($key === 'email' ? 'Gmail' : $channel['label']) }}
                        <span aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </nav>
            <div class="site-footer-v2__partners">
                <h2>{{ __('home.footer.partners_title') }}</h2>
                <div class="site-footer-v2__partner-grid">
                    @foreach ($footerPartners as $index => $partner)
                        <a href="{{ $partner['href'] }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ $partnerAssets[$index] ?? '' }}" alt="" width="58" height="58"
                                 loading="lazy" decoding="async">
                            <span>{{ $partner['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="site-footer-v2__copyright">© {{ now()->year }} {{ __('home.footer.copyright') }}</div>
        </div>
    </div>
    <div class="site-footer-v2__next-shell" data-footer-next-shell>
        <nav class="site-footer-v2__next" aria-label="{{ __('footer_v2.next_label') }}" data-footer-next>
            <p class="site-footer-v2__next-eyebrow">{{ __('footer_v2.keep_scrolling') }}</p>
            <a class="site-footer-v2__next-main" href="{{ route('galeri') }}" data-footer-next-main>
                <span>{{ __('footer_v2.gallery') }}</span>
                <span class="site-footer-v2__next-direction">{{ __('footer_v2.next_label') }} <span aria-hidden="true">⟶</span></span>
                <img src="{{ config('media.static.navigation.gallery') }}" alt="" loading="lazy" decoding="async">
            </a>
            <a class="site-footer-v2__next-alternate" href="{{ route('artikel') }}" data-footer-next-alternate>
                {{ __('footer_v2.articles') }} <span aria-hidden="true">↗</span>
            </a>
        </nav>
    </div>
</footer>
