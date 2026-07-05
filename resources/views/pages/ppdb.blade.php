{{-- PUBLIC_PPDB_DUMMY_FINAL --}}
@extends('layouts.public', ['title' => __('pages.ppdb.title'), 'description' => __('pages.ppdb.description')])

@php($page = __('pages.ppdb'))

@section('content')
  <section class="public-hero public-hero--ppdb" aria-labelledby="ppdb-title">
    <div class="container public-hero__grid">
      <div class="public-hero__copy reveal">
        <a href="{{ route('home') }}" class="link-arrow">{{ __('pages.common.back_home') }}</a>
        <span class="public-eyebrow">{{ $page['hero']['eyebrow'] }}</span>
        <h1 id="ppdb-title" class="public-hero__title">{{ $page['hero']['heading'] }}</h1>
        <p class="public-hero__subtitle">{{ $page['hero']['subtitle'] }}</p>

        <div class="public-hero__actions">
          <a href="{{ __('pages.common.whatsapp_url') }}" class="btn btn--primary">{{ $page['hero']['primary_cta'] }}</a>
          <a href="#alur-ppdb" class="btn btn--ghost">{{ $page['hero']['secondary_cta'] }}</a>
        </div>

        <p class="public-note">{{ $page['hero']['note'] }}</p>

        <div class="public-stat-row" aria-label="{{ $page['hero']['eyebrow'] }}">
          @foreach($page['hero']['stats'] as $stat)
            <div>
              <strong>{{ $stat['value'] }}</strong>
              <span>{{ $stat['label'] }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <div class="ppdb-hero-card reveal" aria-label="{{ $page['hero']['eyebrow'] }}">
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

  <section id="alur-ppdb" class="public-section">
    <div class="container">
      <div class="public-section-head">
        <span class="public-eyebrow">{{ $page['steps_intro']['eyebrow'] }}</span>
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
        <span class="public-eyebrow">{{ $page['program_intro']['eyebrow'] }}</span>
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
          <span class="public-eyebrow">{{ $page['documents_intro']['eyebrow'] }}</span>
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
          <span class="public-eyebrow">{{ $page['timeline_intro']['eyebrow'] }}</span>
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
        <span class="public-eyebrow">{{ $page['faq_intro']['eyebrow'] }}</span>
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
