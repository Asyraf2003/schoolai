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
        <div class="site-footer-v2__grid">
            <div class="site-footer-v2__blank" aria-hidden="true"></div>
            <div class="site-footer-v2__about">
                <p class="site-footer-v2__description">{{ __('home.footer.brand.description') }}</p>
                <div class="site-footer-v2__contact">
                    @foreach (['maps', 'whatsapp'] as $key)
                        @php($channel = $footerChannels->get($key))
                        <a href="{{ $channel['href'] }}" data-footer-preview="{{ $key }}"
                           data-footer-preview-src="{{ $footerAssets[$iconAssets[$key]] }}"
                           @if (str_starts_with($channel['href'], 'https://')) target="_blank" rel="noopener noreferrer" @endif>
                            {{ $key === 'maps' ? 'Maps' : $channel['label'] }} <span aria-hidden="true">↗</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="site-footer-v2__social">
                <nav aria-label="{{ __('footer_v2.social_label') }}">
                    @foreach ($socialIcons as $key)
                        @if (!in_array($key, ['maps', 'whatsapp'], true))
                            @php($channel = $footerChannels->get($key))
                            <a href="{{ $channel['href'] }}" data-footer-preview="{{ $key }}"
                               data-footer-preview-src="{{ $footerAssets[$iconAssets[$key]] }}"
                               @if (str_starts_with($channel['href'], 'https://')) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $key === 'email' ? 'Gmail' : $channel['label'] }} <span aria-hidden="true">↗</span>
                            </a>
                        @endif
                    @endforeach
                </nav>
                <div class="site-footer-v2__partners">
                    <h2>{{ __('home.footer.partners_title') }}</h2>
                    <div class="site-footer-v2__partner-grid">
                        @foreach ($footerPartners as $index => $partner)
                            <a href="{{ $partner['href'] }}" target="_blank" rel="noopener noreferrer"
                               aria-label="{{ $partner['label'] }}">
                                <img src="{{ $partnerAssets[$index] ?? '' }}" alt="{{ $partner['label'] }}"
                                     width="100" height="70" loading="lazy" decoding="async">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="site-footer-v2__copyright">© {{ now()->year }} {{ __('home.footer.copyright') }}</div>
    </div>
    <nav class="site-footer-v2__next" aria-label="{{ __('footer_v2.next_label') }}" data-footer-next>
        <p class="site-footer-v2__next-eyebrow">{{ __('footer_v2.keep_scrolling') }}</p>
        <div class="site-footer-v2__next-links">
            <a href="{{ route('artikel') }}" data-footer-next-link>
                <span>{{ __('footer_v2.articles') }}</span><span aria-hidden="true">↗</span>
                <img src="{{ config('media.static.navigation.article') }}" alt="" loading="lazy" decoding="async">
            </a>
            <a href="{{ route('galeri') }}" data-footer-next-link>
                <span>{{ __('footer_v2.gallery') }}</span><span aria-hidden="true">↗</span>
                <img src="{{ config('media.static.navigation.gallery') }}" alt="" loading="lazy" decoding="async">
            </a>
        </div>
    </nav>
</footer>
