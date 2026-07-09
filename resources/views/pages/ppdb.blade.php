{{-- PUBLIC_PPDB_DUMMY_FINAL --}}
@extends('layouts.public', ['title' => __('pages.ppdb.title'), 'description' => __('pages.ppdb.description')])

@php
  $page = __('pages.ppdb');
  $ppdbAdmission = $ppdbAdmission ?? null;
  $ppdbFormUrl = $ppdbAdmission?->publicRegistrationUrl();
  $ppdbInfoUrl = $ppdbAdmission?->publicInformationUrl();
  $ppdbIsOpen = (bool) ($ppdbAdmission?->isRegistrationOpen() ?? false);
  $isEnglish = app()->getLocale() === 'en';
  $ppdbRegisterButtonLabel = $isEnglish ? 'Apply Online Now' : 'Daftar PPDB Online';
  $ppdbGuideButtonLabel = $isEnglish ? 'View Guide / Requirements' : 'Lihat Panduan / Syarat PPDB';
  $ppdbFinalButtonLabel = $isEnglish ? 'Apply Now' : 'Daftar Sekarang';
  $ppdbClosedTitle = $isEnglish ? 'Admission is currently closed' : 'Pendaftaran saat ini sedang ditutup';
  $ppdbClosedText = $isEnglish
      ? 'Sorry, admission registration is not open at the moment. Please check this page again later or contact the school admin.'
      : 'Maaf, pendaftaran saat ini sedang ditutup. Silakan cek halaman ini kembali nanti atau hubungi admin sekolah.';
  $ppdbClosedButton = $isEnglish ? 'I understand' : 'Saya Mengerti';
@endphp

@section('content')
  <style>
    .public-hero__actions .btn--ppdb-register {
      background: #137a4c;
      color: #ffffff;
      border: 2px solid rgba(255, 255, 255, 0.72);
      box-shadow: 0 16px 34px rgba(19, 122, 76, 0.34);
      font-weight: 900;
      letter-spacing: 0.01em;
      text-shadow: 0 1px 1px rgba(0, 0, 0, 0.26);
    }

    .public-hero__actions .btn--ppdb-register:hover {
      background: #0f6a41;
      box-shadow: 0 20px 42px rgba(19, 122, 76, 0.42);
    }

    .public-hero__actions .btn--ppdb-guide {
      background: #ffffff;
      color: #20223f;
      border: 2px solid rgba(19, 122, 76, 0.34);
      box-shadow: 0 12px 26px rgba(32, 34, 63, 0.12);
      font-weight: 900;
    }

    .public-hero__actions .btn--ppdb-guide:hover {
      border-color: #137a4c;
      box-shadow: 0 16px 34px rgba(32, 34, 63, 0.16);
    }

    .public-hero__actions .btn:focus-visible {
      outline: 4px solid rgba(255, 201, 60, 0.75);
      outline-offset: 4px;
    }

    .ppdb-closed-modal {
      position: fixed;
      inset: 0;
      z-index: 1200;
      display: grid;
      place-items: center;
      padding: 24px;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: opacity 0.18s ease, visibility 0.18s ease;
    }

    .ppdb-closed-modal:target {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
    }

    .ppdb-closed-modal__backdrop {
      position: absolute;
      inset: 0;
      background: rgba(16, 24, 40, 0.52);
      backdrop-filter: blur(7px);
    }

    .ppdb-closed-modal__panel {
      position: relative;
      z-index: 1;
      width: min(440px, 100%);
      padding: 28px;
      border-radius: 28px;
      background: #ffffff;
      color: #20223f;
      box-shadow: 0 26px 70px rgba(16, 24, 40, 0.24);
      text-align: center;
    }

    .ppdb-closed-modal__icon {
      width: 58px;
      height: 58px;
      display: grid;
      place-items: center;
      margin: 0 auto 14px;
      border-radius: 22px;
      background: #fff2c6;
      font-size: 1.9rem;
    }

    .ppdb-closed-modal__panel h2 {
      margin: 0;
      font-size: clamp(1.45rem, 4vw, 1.9rem);
      line-height: 1.12;
      letter-spacing: -0.04em;
    }

    .ppdb-closed-modal__panel p {
      margin: 12px 0 0;
      color: #6b7280;
      line-height: 1.7;
    }

    .ppdb-closed-modal__close {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 42px;
      margin-top: 20px;
      padding: 10px 18px;
      border-radius: 999px;
      background: #20223f;
      color: #ffffff;
      font-weight: 900;
      text-decoration: none;
    }

    .ppdb-liftoff {
      position: relative;
      overflow: hidden;
      padding: clamp(76px, 9vw, 126px) 0;
      background:
        radial-gradient(circle at 11% 18%, rgba(255, 159, 90, 0.28), transparent 28%),
        radial-gradient(circle at 90% 9%, rgba(127, 199, 224, 0.36), transparent 30%),
        linear-gradient(180deg, #fffbf4 0%, #fff8ec 100%);
      color: #181229;
    }

    .ppdb-liftoff::before {
      content: "";
      position: absolute;
      inset: 32px 10px;
      border: 2px dashed rgba(24, 18, 41, 0.13);
      border-radius: 38px;
      pointer-events: none;
    }

    .ppdb-liftoff__top {
      position: relative;
      z-index: 2;
      display: grid;
      justify-items: center;
      gap: 22px;
      margin-bottom: clamp(58px, 7vw, 92px);
      text-align: center;
    }

    .ppdb-liftoff__switch {
      display: inline-grid;
      grid-template-columns: 1fr 1fr;
      gap: 4px;
      padding: 7px;
      border-radius: 999px;
      background:
        linear-gradient(#fffbf4, #fffbf4) padding-box,
        linear-gradient(90deg, #cfe6ff, #aaaafa 45%, #fa946c) border-box;
      border: 3px solid transparent;
      box-shadow: 0 16px 34px rgba(24, 18, 41, 0.09);
    }

    .ppdb-liftoff__switch span {
      min-width: min(40vw, 178px);
      padding: 15px 20px;
      border-radius: 999px;
      color: #181229;
      font-weight: 900;
      line-height: 1;
      white-space: nowrap;
    }

    .ppdb-liftoff__switch span:first-child {
      background: #181229;
      color: #ffffff;
      box-shadow: 0 10px 22px rgba(24, 18, 41, 0.18);
    }

    .ppdb-liftoff__top h2 {
      max-width: 760px;
      font-size: clamp(2rem, 4.4vw, 4.6rem);
      line-height: 0.98;
      letter-spacing: -0.07em;
      font-weight: 950;
    }

    .ppdb-liftoff__top p {
      max-width: 640px;
      color: rgba(24, 18, 41, 0.68);
      font-size: clamp(1rem, 1.5vw, 1.2rem);
      line-height: 1.65;
    }

    .ppdb-liftoff__rail {
      position: absolute;
      left: 50%;
      top: 330px;
      bottom: 170px;
      width: min(720px, 72vw);
      transform: translateX(-50%);
      pointer-events: none;
      z-index: 1;
    }

    .ppdb-liftoff__rail svg {
      width: 100%;
      height: 100%;
      overflow: visible;
    }

    .ppdb-liftoff__rail path {
      fill: none;
      stroke: url(#ppdbLiftoffRailGradient);
      stroke-width: 8;
      stroke-linecap: round;
      stroke-linejoin: round;
      opacity: 0.72;
      filter: drop-shadow(0 8px 12px rgba(24, 18, 41, 0.08));
    }

    .ppdb-liftoff__stack {
      position: relative;
      z-index: 2;
      display: grid;
      gap: clamp(46px, 6.5vw, 82px);
    }

    .ppdb-liftoff-card {
      display: grid;
      grid-template-columns: minmax(300px, 0.95fr) minmax(260px, 0.75fr);
      align-items: center;
      gap: clamp(34px, 6vw, 80px);
      min-height: 430px;
    }

    .ppdb-liftoff-card:nth-child(even) {
      grid-template-columns: minmax(260px, 0.75fr) minmax(300px, 0.95fr);
    }

    .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__visual {
      order: 2;
    }

    .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__text {
      order: 1;
    }

    .ppdb-liftoff-card__visual {
      position: relative;
      min-height: clamp(300px, 38vw, 430px);
      border-radius: 28px;
      overflow: hidden;
      background:
        linear-gradient(135deg, rgba(255, 46, 125, 0.92), rgba(255, 102, 33, 0.84) 36%, rgba(210, 231, 255, 0.8) 100%),
        #f7f1f0;
      box-shadow: 0 28px 72px rgba(24, 18, 41, 0.14);
      isolation: isolate;
    }

    .ppdb-liftoff-card:nth-child(2) .ppdb-liftoff-card__visual {
      background:
        linear-gradient(135deg, rgba(210, 231, 255, 0.92), rgba(170, 171, 250, 0.9) 46%, rgba(250, 148, 108, 0.76) 100%),
        #f7f1f0;
    }

    .ppdb-liftoff-card:nth-child(3) .ppdb-liftoff-card__visual {
      background:
        linear-gradient(135deg, rgba(255, 201, 60, 0.78), rgba(126, 217, 180, 0.74) 42%, rgba(183, 163, 224, 0.82) 100%),
        #f7f1f0;
    }

    .ppdb-liftoff-card__visual::after {
      content: "";
      position: absolute;
      inset: auto -16% -28% 8%;
      height: 56%;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.34);
      filter: blur(16px);
      z-index: -1;
    }

    .ppdb-liftoff-ui {
      position: absolute;
      inset: 10% 9% auto;
      display: grid;
      gap: 22px;
    }

    .ppdb-liftoff-ui__panel {
      padding: clamp(20px, 3vw, 30px);
      border-radius: 24px;
      background: #ffffff;
      box-shadow: 0 18px 46px rgba(24, 18, 41, 0.16);
    }

    .ppdb-liftoff-ui__panel h3 {
      margin: 0 0 16px;
      font-size: clamp(1rem, 1.4vw, 1.2rem);
      letter-spacing: -0.03em;
    }

    .ppdb-liftoff-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .ppdb-liftoff-tags span {
      padding: 8px 13px;
      border-radius: 999px;
      background: rgba(170, 171, 250, 0.28);
      color: rgba(24, 18, 41, 0.76);
      font-weight: 800;
      font-size: 0.86rem;
    }

    .ppdb-liftoff-list {
      display: grid;
      gap: 12px;
    }

    .ppdb-liftoff-list__item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 14px;
      border-radius: 16px;
      background: #ffffff;
      border: 1px solid rgba(24, 18, 41, 0.06);
      box-shadow: 0 10px 26px rgba(24, 18, 41, 0.07);
      color: rgba(24, 18, 41, 0.72);
      font-weight: 800;
    }

    .ppdb-liftoff-list__item span {
      width: 36px;
      height: 36px;
      display: grid;
      place-items: center;
      border-radius: 12px;
      background: #181229;
      color: #ffffff;
      flex: 0 0 auto;
    }

    .ppdb-liftoff-chip {
      position: absolute;
      right: 8%;
      bottom: 9%;
      max-width: 74%;
      padding: 16px 18px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.88);
      box-shadow: 0 16px 44px rgba(24, 18, 41, 0.18);
      color: rgba(24, 18, 41, 0.76);
      font-weight: 900;
      backdrop-filter: blur(10px);
    }

    .ppdb-liftoff-card__text {
      justify-self: center;
      max-width: 460px;
    }

    .ppdb-liftoff-step {
      width: 42px;
      height: 42px;
      display: grid;
      place-items: center;
      margin-bottom: 22px;
      border-radius: 999px;
      background:
        linear-gradient(#fffbf4, #fffbf4) padding-box,
        linear-gradient(135deg, #cfe6ff, #aaaafa 55%, #fa946c) border-box;
      border: 3px solid transparent;
      color: #181229;
      font-weight: 950;
    }

    .ppdb-liftoff-card__text h3 {
      margin: 0;
      font-size: clamp(1.8rem, 3.2vw, 3.2rem);
      line-height: 1.02;
      letter-spacing: -0.065em;
      font-weight: 950;
    }

    .ppdb-liftoff-card__text p {
      margin-top: 20px;
      color: rgba(24, 18, 41, 0.68);
      font-size: clamp(1rem, 1.35vw, 1.16rem);
      line-height: 1.65;
    }

    .ppdb-liftoff-card__text strong {
      display: inline-flex;
      margin-top: 18px;
      padding: 10px 15px;
      border-radius: 999px;
      background: #ffffff;
      box-shadow: 0 10px 24px rgba(24, 18, 41, 0.08);
      color: #137a4c;
      font-weight: 950;
    }

    .ppdb-liftoff__cta {
      position: relative;
      z-index: 2;
      display: grid;
      justify-items: center;
      gap: 18px;
      max-width: 720px;
      margin: clamp(56px, 7vw, 92px) auto 0;
      text-align: center;
    }

    .ppdb-liftoff__cta p {
      color: rgba(24, 18, 41, 0.68);
      font-size: 1.04rem;
      line-height: 1.7;
    }

    @media (max-width: 900px) {
      .ppdb-liftoff::before {
        inset: 16px;
        border-radius: 28px;
      }

      .ppdb-liftoff__rail {
        display: none;
      }

      .ppdb-liftoff-card,
      .ppdb-liftoff-card:nth-child(even) {
        grid-template-columns: 1fr;
        min-height: 0;
      }

      .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__visual,
      .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__text {
        order: initial;
      }

      .ppdb-liftoff-card__text {
        max-width: none;
        justify-self: start;
      }
    }

    @media (max-width: 620px) {
      .ppdb-liftoff {
        padding-block: 64px;
      }

      .ppdb-liftoff__switch {
        width: 100%;
      }

      .ppdb-liftoff__switch span {
        min-width: 0;
        padding-inline: 12px;
        font-size: 0.9rem;
      }

      .ppdb-liftoff-card__visual {
        min-height: 320px;
        border-radius: 24px;
      }

      .ppdb-liftoff-ui {
        inset: 9% 7% auto;
      }

      .ppdb-liftoff-chip {
        left: 7%;
        right: 7%;
        max-width: none;
      }
    }
  </style>

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

  {{-- PPDB_LIFTOFF_INSPIRED_SECTION --}}
  <section class="ppdb-liftoff" aria-labelledby="ppdb-journey-title">
    <div class="container">
      <div class="ppdb-liftoff__top reveal">
        <div class="ppdb-liftoff__switch" aria-hidden="true">
          <span>{{ $isEnglish ? 'For parents' : 'Untuk orang tua' }}</span>
          <span>{{ $isEnglish ? 'For school' : 'Untuk sekolah' }}</span>
        </div>
        <h2 id="ppdb-journey-title">{{ $page['psb_showcase']['title'] }}</h2>
        <p>{{ $page['psb_showcase']['subtitle'] }}</p>
      </div>

      <div class="ppdb-liftoff__rail" aria-hidden="true">
        <svg viewBox="0 0 720 1080" preserveAspectRatio="none">
          <defs>
            <linearGradient id="ppdbLiftoffRailGradient" x1="0" x2="1" y1="0" y2="1">
              <stop offset="0%" stop-color="#d2e7ff" />
              <stop offset="45%" stop-color="#aaaafa" />
              <stop offset="100%" stop-color="#fa946c" />
            </linearGradient>
          </defs>
          <path d="M 492 18 C 598 154 572 250 420 292 L 185 356 C 61 390 64 520 190 548 L 556 628 C 700 660 696 810 555 850 L 212 948 C 116 976 95 1040 164 1072" />
        </svg>
      </div>

      <div class="ppdb-liftoff__stack">
        @foreach($page['psb_showcase']['items'] as $item)
          <article class="ppdb-liftoff-card reveal">
            <div class="ppdb-liftoff-card__visual" aria-hidden="true">
              <div class="ppdb-liftoff-ui">
                <div class="ppdb-liftoff-ui__panel">
                  <h3>{{ $item['title'] }}</h3>
                  <div class="ppdb-liftoff-tags">
                    <span>{{ $item['highlight'] }}</span>
                    <span>{{ $page['hero']['stats'][$loop->index % count($page['hero']['stats'])]['label'] ?? $page['hero']['note'] }}</span>
                    <span>+{{ $loop->iteration }}</span>
                  </div>
                </div>

                <div class="ppdb-liftoff-ui__panel">
                  <div class="ppdb-liftoff-list">
                    <div class="ppdb-liftoff-list__item"><span>{{ $item['icon'] }}</span>{{ $isEnglish ? 'Family note' : 'Catatan keluarga' }}</div>
                    <div class="ppdb-liftoff-list__item"><span>✓</span>{{ $isEnglish ? 'Admin follow-up' : 'Follow-up admin' }}</div>
                  </div>
                </div>
              </div>

              <div class="ppdb-liftoff-chip">
                {{ $isEnglish ? 'PPDB flow stays clear, warm, and easy to share.' : 'Alur PPDB tetap jelas, hangat, dan mudah dibagikan.' }}
              </div>
            </div>

            <div class="ppdb-liftoff-card__text">
              <div class="ppdb-liftoff-step">{{ $item['number'] }}</div>
              <h3>{{ $item['title'] }}</h3>
              <p>{{ $item['description'] }}</p>
              <strong>{{ $item['highlight'] }}</strong>
            </div>
          </article>
        @endforeach
      </div>

      <div class="ppdb-liftoff__cta reveal">
        <p>{{ $page['psb_showcase']['note'] }}</p>
        <a href="#alur-ppdb" class="btn btn--primary">{{ $page['psb_showcase']['button'] }}</a>
      </div>
    </div>
  </section>
  {{-- /PPDB_LIFTOFF_INSPIRED_SECTION --}}

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
        @if ($ppdbIsOpen && $ppdbFormUrl)
          <a href="{{ $ppdbFormUrl }}" class="btn btn--white" target="_blank" rel="noopener noreferrer">{{ $ppdbFinalButtonLabel }}</a>
        @else
          <a href="#ppdb-closed-modal" class="btn btn--white">{{ $ppdbFinalButtonLabel }}</a>
        @endif
      </div>
    </div>
  </section>

  <section class="ppdb-closed-modal" id="ppdb-closed-modal" role="dialog" aria-modal="true" aria-labelledby="ppdb-closed-title" aria-describedby="ppdb-closed-description">
    <a href="#" class="ppdb-closed-modal__backdrop" aria-label="{{ __('pages.common.close') }}"></a>

    <div class="ppdb-closed-modal__panel">
      <div class="ppdb-closed-modal__icon" aria-hidden="true">🕊️</div>
      <h2 id="ppdb-closed-title">{{ $ppdbClosedTitle }}</h2>
      <p id="ppdb-closed-description">{{ $ppdbClosedText }}</p>
      <a href="#" class="ppdb-closed-modal__close">{{ $ppdbClosedButton }}</a>
    </div>
  </section>
@endsection
