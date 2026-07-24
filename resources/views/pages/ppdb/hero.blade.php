  <section class="public-hero public-hero--ppdb" aria-labelledby="ppdb-title">
    <div class="container public-hero__grid">
      <div class="public-hero__copy reveal">
        <h1 id="ppdb-title" class="public-hero__title">{{ $page['hero']['heading'] }}</h1>
        <p class="public-hero__subtitle">{{ $page['hero']['subtitle'] }}</p>
        <div class="public-hero__actions">
          @if ($ppdbIsOpen && $ppdbFormUrl)
            <a href="{{ $ppdbFormUrl }}" class="btn btn--primary btn--ppdb-register" target="_blank" rel="noopener noreferrer">{{ $ppdbRegisterButtonLabel }}</a>
          @else
            <a href="#ppdb-closed-modal" class="btn btn--primary btn--ppdb-register">{{ $ppdbRegisterButtonLabel }}</a>
          @endif
          @if ($ppdbInfoUrl)
            <a href="{{ $ppdbInfoUrl }}" class="btn btn--ghost btn--ppdb-guide" target="_blank" rel="noopener noreferrer">{{ $ppdbGuideButtonLabel }}</a>
          @else
            <a href="#alur-ppdb" class="btn btn--ghost btn--ppdb-guide">{{ $ppdbGuideButtonLabel }}</a>
          @endif
        </div>
        <p class="public-note">{{ $page['hero']['note'] }}</p>
        <div class="public-stat-row" aria-label="{{ $page['hero']['heading'] ?? $page['title'] }}">
          @foreach($page['hero']['stats'] as $stat)
            <div><strong>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>
          @endforeach
        </div>
      </div>
      <div class="ppdb-hero-card reveal" aria-label="{{ $page['hero']['heading'] ?? $page['title'] }}">
        <div class="ppdb-hero-card__orb" aria-hidden="true">✨</div>
        @foreach($page['hero']['mini_cards'] as $card)
          <article class="ppdb-mini-card"><span aria-hidden="true">{{ $card['icon'] }}</span><div><h2>{{ $card['title'] }}</h2><p>{{ $card['text'] }}</p></div></article>
        @endforeach
      </div>
    </div>
  </section>
