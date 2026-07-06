{{-- PUBLIC_PPDB_DUMMY_FINAL --}}
@extends('layouts.public', ['title' => __('pages.ppdb.title'), 'description' => __('pages.ppdb.description')])

@php($page = __('pages.ppdb'))

@section('content')
  <section class="public-hero public-hero--ppdb" aria-labelledby="ppdb-title">
    <div class="container public-hero__grid">
      <div class="public-hero__copy reveal">
        <a href="{{ route('home') }}" class="link-arrow">{{ __('pages.common.back_home') }}</a>
        <h1 id="ppdb-title" class="public-hero__title">{{ $page['hero']['heading'] }}</h1>
        <p class="public-hero__subtitle">{{ $page['hero']['subtitle'] }}</p>

        <div class="public-hero__actions">
          <a href="{{ __('pages.common.whatsapp_url') }}" class="btn btn--primary">{{ $page['hero']['primary_cta'] }}</a>
          <a href="#alur-ppdb" class="btn btn--ghost">{{ $page['hero']['secondary_cta'] }}</a>
        </div>

        <p class="public-note">{{ $page['hero']['note'] }}</p>

        <div class="public-stat-row" aria-label="{{ $page['hero']['heading'] ?? $page['title'] }}">
          @foreach($page['hero']['stats'] as $stat)
            <div>
              <strong>{{ $stat['value'] }}</strong>
              <span>{{ $stat['label'] }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <div class="ppdb-hero-card reveal" aria-label="{{ $page['hero']['heading'] ?? $page['title'] }}">
        <div class="ppdb-hero-card__orb" aria-hidden="true">✨</div>
        @foreach($page['hero']['mini_cards'] as $card)
          <article class="ppdb-mini-card">
            <span aria-hidden="true">{{ $card['icon'] }}</span>
            <div>
              <h2>{{ $card['title'] }}</h2>
              <p>{{ $card['text'] }}</p>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>


  {{-- PPDB_PSB_INSPIRED_SECTION_FINAL --}}
  <section class="ppdb-journey-section text-center position-relative" aria-labelledby="ppdb-journey-title">
    <div class="container">
      <div class="ppdb-journey-head reveal">
        <div class="ppdb-title-effect" aria-hidden="true">
          <span class="bar bar-top"></span>
          <span class="bar bar-right"></span>
          <span class="bar bar-bottom"></span>
          <span class="bar bar-left"></span>
        </div>
        <h2 id="ppdb-journey-title">{{ $page['psb_showcase']['title'] }}</h2>
        <p>{{ $page['psb_showcase']['subtitle'] }}</p>
      </div>

      <div class="ppdb-flight-stage" aria-hidden="true">
        <svg class="ppdb-flight-svg" viewBox="0 0 900 130" preserveAspectRatio="none">
          <path
            class="ppdb-flight-path"
            d="M 40 76 C 150 8, 250 118, 360 58 S 570 18, 670 72 S 810 112, 860 40"
          />
        </svg>
        <span class="ppdb-flight-plane" aria-hidden="true">
          <svg class="ppdb-paper-plane" viewBox="0 0 96 96" role="img" focusable="false">
            <path class="ppdb-paper-plane__body" d="M8 47.5 84 14 61 84 43.5 57.5 27 72 31.5 52.5 8 47.5Z" />
            <path class="ppdb-paper-plane__fold" d="M31.5 52.5 84 14 43.5 57.5 61 84" />
            <path class="ppdb-paper-plane__shine" d="M43.5 57.5 84 14 31.5 52.5" />
          </svg>
        </span>
      </div>

      <div class="ppdb-work-grid">
        @foreach($page['psb_showcase']['items'] as $item)
          <article class="ppdb-work-process reveal">
            @if(! $loop->last)
              <div class="ppdb-box-loader" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
              </div>
            @endif

            <div class="ppdb-step-num-box">
              <div class="ppdb-step-icon">
                <span aria-hidden="true">{{ $item['icon'] }}</span>
              </div>
              <div class="ppdb-step-num">{{ $item['number'] }}</div>
            </div>

            <div class="ppdb-step-desc">
              <h3>{{ $item['title'] }}</h3>
              <p>{{ $item['description'] }}</p>
              <strong>{{ $item['highlight'] }}</strong>
            </div>
          </article>
        @endforeach
      </div>

      <div class="ppdb-journey-cta reveal">
        <p>{{ $page['psb_showcase']['note'] }}</p>
        <a href="#alur-ppdb" class="btn btn--primary">{{ $page['psb_showcase']['button'] }}</a>
      </div>
    </div>
  </section>
  {{-- /PPDB_PSB_INSPIRED_SECTION_FINAL --}}


  <section id="alur-ppdb" class="public-section">
    <div class="container">
      <div class="public-section-head">
        <h2>{{ $page['steps_intro']['heading'] }}</h2>
        <p>{{ $page['steps_intro']['subtitle'] }}</p>
      </div>

      <div class="public-step-grid">
        @foreach($page['steps'] as $index => $step)
          <article class="public-step-card reveal">
            <span class="public-step-card__number">{{ sprintf('%02d', $index + 1) }}</span>
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['text'] }}</p>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="public-section public-section--soft">
    <div class="container">
      <div class="public-section-head">
        <h2>{{ $page['program_intro']['heading'] }}</h2>
      </div>

      <div class="program-public-grid">
        @foreach($page['programs'] as $program)
          <article class="program-public-card reveal">
            <span class="program-public-card__icon" aria-hidden="true">{{ $program['icon'] }}</span>
            <p class="program-public-card__age">{{ $program['age'] }}</p>
            <h3>{{ $program['name'] }}</h3>
            <p>{{ $program['text'] }}</p>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="public-section">
    <div class="container ppdb-info-grid">
      <div>
        <div class="public-section-head public-section-head--compact">
          <h2>{{ $page['documents_intro']['heading'] }}</h2>
        </div>

        <ul class="document-list">
          @foreach($page['documents'] as $document)
            <li class="reveal">{{ $document }}</li>
          @endforeach
        </ul>
      </div>

      <div>
        <div class="public-section-head public-section-head--compact">
          <h2>{{ $page['timeline_intro']['heading'] }}</h2>
        </div>

        <div class="timeline-list">
          @foreach($page['timeline'] as $item)
            <article class="timeline-card reveal">
              <span>{{ $item['date'] }}</span>
              <h3>{{ $item['title'] }}</h3>
              <p>{{ $item['text'] }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="public-section public-section--soft">
    <div class="container">
      <div class="public-section-head">
        <h2>{{ $page['faq_intro']['heading'] }}</h2>
      </div>

      <div class="faq-grid">
        @foreach($page['faq'] as $faq)
          <details class="faq-card reveal">
            <summary>{{ $faq['q'] }}</summary>
            <p>{{ $faq['a'] }}</p>
          </details>
        @endforeach
      </div>
    </div>
  </section>

  <section class="public-final-cta">
    <div class="container">
      <div class="public-final-cta__box reveal">
        <span aria-hidden="true">📮</span>
        <h2>{{ $page['final_cta']['heading'] }}</h2>
        <p>{{ $page['final_cta']['subtitle'] }}</p>
        <a href="{{ __('pages.common.whatsapp_url') }}" class="btn btn--white">{{ $page['final_cta']['button'] }}</a>
      </div>
    </div>
  </section>
@endsection
