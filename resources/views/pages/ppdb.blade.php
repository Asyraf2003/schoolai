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
  $ppdbShowcaseItems = collect($ppdbShowcaseItems ?? []);
  $ppdbShowcaseByAudience = [
      'parents' => $ppdbShowcaseItems->where('audience', 'parents')->values(),
      'school' => $ppdbShowcaseItems->where('audience', 'school')->values(),
  ];
  $ppdbAudienceLabels = [
      'parents' => $isEnglish ? 'For parents' : 'Untuk orang tua',
      'school' => $isEnglish ? 'For school' : 'Untuk sekolah',
  ];
  $ppdbAvailableAudiences = collect(array_keys($ppdbShowcaseByAudience))
      ->filter(fn (string $audience): bool => $ppdbShowcaseByAudience[$audience]->isNotEmpty())
      ->values();
  $ppdbInitialAudience = $ppdbAvailableAudiences->first() ?? 'parents';
@endphp

@section('content')
  <style>
    .public-hero__actions .btn--ppdb-register{background:#137a4c;color:#fff;border:2px solid rgba(255,255,255,.72);box-shadow:0 16px 34px rgba(19,122,76,.34);font-weight:900;letter-spacing:.01em;text-shadow:0 1px 1px rgba(0,0,0,.26)}
    .public-hero__actions .btn--ppdb-register:hover{background:#0f6a41;box-shadow:0 20px 42px rgba(19,122,76,.42)}
    .public-hero__actions .btn--ppdb-guide{background:#fff;color:#20223f;border:2px solid rgba(19,122,76,.34);box-shadow:0 12px 26px rgba(32,34,63,.12);font-weight:900}
    .public-hero__actions .btn--ppdb-guide:hover{border-color:#137a4c;box-shadow:0 16px 34px rgba(32,34,63,.16)}
    .public-hero__actions .btn:focus-visible{outline:4px solid rgba(255,201,60,.75);outline-offset:4px}

    .ppdb-closed-modal{position:fixed;inset:0;z-index:1200;display:grid;place-items:center;padding:24px;opacity:0;visibility:hidden;pointer-events:none;transition:opacity .18s ease,visibility .18s ease}
    .ppdb-closed-modal:target{opacity:1;visibility:visible;pointer-events:auto}
    .ppdb-closed-modal__backdrop{position:absolute;inset:0;background:rgba(16,24,40,.52);backdrop-filter:blur(7px)}
    .ppdb-closed-modal__panel{position:relative;z-index:1;width:min(440px,100%);padding:28px;border-radius:28px;background:#fff;color:#20223f;box-shadow:0 26px 70px rgba(16,24,40,.24);text-align:center}
    .ppdb-closed-modal__icon{width:58px;height:58px;display:grid;place-items:center;margin:0 auto 14px;border-radius:22px;background:#fff2c6;font-size:1.9rem}
    .ppdb-closed-modal__panel h2{margin:0;font-size:clamp(1.45rem,4vw,1.9rem);line-height:1.12;letter-spacing:-.04em}
    .ppdb-closed-modal__panel p{margin:12px 0 0;color:#6b7280;line-height:1.7}
    .ppdb-closed-modal__close{display:inline-flex;align-items:center;justify-content:center;min-height:42px;margin-top:20px;padding:10px 18px;border-radius:999px;background:#20223f;color:#fff;font-weight:900;text-decoration:none}

    .ppdb-liftoff{position:relative;overflow:hidden;padding:clamp(76px,9vw,126px) 0;background:radial-gradient(circle at 11% 18%,rgba(255,159,90,.28),transparent 28%),radial-gradient(circle at 90% 9%,rgba(127,199,224,.36),transparent 30%),linear-gradient(180deg,#fffbf4 0%,#fff8ec 100%);color:#181229;transition:background .28s ease,color .28s ease}
    .ppdb-liftoff[data-active-audience=school]{background:radial-gradient(circle at 12% 14%,rgba(163,160,255,.22),transparent 28%),radial-gradient(circle at 86% 8%,rgba(255,159,90,.16),transparent 28%),linear-gradient(180deg,#181229 0%,#201b35 100%);color:#fff}
    .ppdb-liftoff::before{content:"";position:absolute;inset:32px 10px;border:2px dashed rgba(24,18,41,.13);border-radius:38px;pointer-events:none}
    .ppdb-liftoff[data-active-audience=school]::before{border-color:rgba(255,255,255,.22)}
    .ppdb-liftoff__top{position:relative;z-index:3;display:grid;justify-items:center;gap:22px;margin-bottom:clamp(70px,8vw,112px);text-align:center}
    .ppdb-liftoff__switch{position:relative;display:inline-grid;grid-template-columns:1fr 1fr;gap:4px;padding:7px;border-radius:999px;background:linear-gradient(#fffbf4,#fffbf4) padding-box,linear-gradient(90deg,#cfe6ff,#aaaafa 45%,#fa946c) border-box;border:3px solid transparent;box-shadow:0 16px 34px rgba(24,18,41,.09);isolation:isolate;transition:background .24s ease,box-shadow .24s ease}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__switch{background:linear-gradient(#181229,#181229) padding-box,linear-gradient(90deg,#a3a0ff,#fff 48%,#fa946c) border-box;box-shadow:0 18px 40px rgba(0,0,0,.24)}
    .ppdb-liftoff__switch::before{content:"";position:absolute;top:7px;left:7px;width:calc(50% - 9px);height:calc(100% - 14px);border-radius:999px;background:#181229;box-shadow:0 10px 22px rgba(24,18,41,.18);transform:translateX(0);transition:transform .24s ease,background .24s ease;z-index:-1}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__switch::before{transform:translateX(calc(100% + 4px));background:#fff;box-shadow:0 10px 22px rgba(255,255,255,.16)}
    .ppdb-liftoff__tab{min-width:min(40vw,178px);padding:15px 20px;border:0;background:transparent;border-radius:999px;color:#181229;font:inherit;font-weight:900;line-height:1;white-space:nowrap;position:relative;z-index:1;transition:color .2s ease,opacity .2s ease;cursor:pointer}
    .ppdb-liftoff__tab.is-active{color:#fff}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__tab{color:rgba(255,255,255,.86)}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__tab.is-active{color:#181229}
    .ppdb-liftoff__tab:disabled{opacity:.42;cursor:not-allowed}
    .ppdb-liftoff__top h2{max-width:780px;font-size:clamp(2rem,4.4vw,4.6rem);line-height:.98;letter-spacing:-.07em;font-weight:950}
    .ppdb-liftoff__top p{max-width:640px;color:rgba(24,18,41,.68);font-size:clamp(1rem,1.5vw,1.2rem);line-height:1.65}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__top p,.ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__cta p{color:rgba(255,255,255,.72)}

    .ppdb-liftoff-panel{position:relative;min-height:980px}
    .ppdb-liftoff-panel[hidden]{display:none}
    .ppdb-liftoff__rail{position:absolute;left:50%;top:-18px;bottom:-18px;width:min(940px,86vw);transform:translateX(-50%);pointer-events:none;z-index:1}
    .ppdb-liftoff__rail svg{width:100%;height:100%;overflow:visible}
    .ppdb-liftoff .reveal{transform:translateY(16px);transition:opacity .45s ease,transform .45s ease}
    .ppdb-liftoff__rail path{fill:none;stroke-linecap:round;stroke-linejoin:round}
    .ppdb-liftoff__rail-base{stroke:rgba(24,18,41,.15);stroke-width:18;stroke-dasharray:2 28;filter:drop-shadow(0 14px 18px rgba(24,18,41,.12))}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__rail-base{stroke:rgba(255,255,255,.24)}
    .ppdb-liftoff__rail-progress{stroke:url(#ppdbLiftoffRailGradientParents);stroke-width:13;opacity:.98;filter:url(#ppdbLiftoffRailGlow);transition:stroke-dashoffset .12s linear}
    .ppdb-liftoff-panel--school .ppdb-liftoff__rail-progress{stroke:url(#ppdbLiftoffRailGradientSchool)}
    .ppdb-liftoff__rail-dot{fill:#ffffff;stroke:#fa946c;stroke-width:8;filter:url(#ppdbLiftoffRailDotGlow);transition:cx .12s linear,cy .12s linear,opacity .16s ease}
    .ppdb-liftoff-panel--school .ppdb-liftoff__rail-dot{stroke:#a3a0ff}
    .ppdb-liftoff__stack{position:relative;z-index:2;display:grid;gap:clamp(96px,13vw,190px)}
    .ppdb-liftoff-card{display:grid;grid-template-columns:minmax(300px,.95fr) minmax(260px,.75fr);align-items:center;gap:clamp(42px,7vw,104px);min-height:560px}
    .ppdb-liftoff-card:nth-child(even){grid-template-columns:minmax(260px,.75fr) minmax(300px,.95fr)}
    .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__visual{order:2}
    .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__text{order:1}
    .ppdb-liftoff-card__visual{position:relative;min-height:clamp(350px,40vw,500px);border-radius:28px;overflow:hidden;background:linear-gradient(135deg,rgba(255,46,125,.92),rgba(255,102,33,.84) 36%,rgba(210,231,255,.8) 100%),#f7f1f0;box-shadow:0 28px 72px rgba(24,18,41,.14);isolation:isolate}
    .ppdb-liftoff-card:nth-child(3n + 2) .ppdb-liftoff-card__visual{background:linear-gradient(135deg,rgba(210,231,255,.92),rgba(170,171,250,.9) 46%,rgba(250,148,108,.76) 100%),#f7f1f0}
    .ppdb-liftoff-card:nth-child(3n) .ppdb-liftoff-card__visual{background:linear-gradient(135deg,rgba(255,201,60,.78),rgba(126,217,180,.74) 42%,rgba(183,163,224,.82) 100%),#f7f1f0}
    .ppdb-liftoff-panel--school .ppdb-liftoff-card__visual{background:linear-gradient(135deg,rgba(163,160,255,.88),rgba(75,68,140,.9) 48%,rgba(249,115,22,.44) 100%),#221d3d;box-shadow:0 28px 72px rgba(0,0,0,.24)}
    .ppdb-liftoff-media,.ppdb-liftoff-media img,.ppdb-liftoff-media iframe{position:absolute;inset:0;width:100%;height:100%;border:0;display:block;object-fit:cover;background:#111827}
    .ppdb-liftoff-media::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(24,18,41,.02),rgba(24,18,41,.28));pointer-events:none}
    .ppdb-liftoff-ui{position:absolute;inset:10% 9% auto;display:grid;gap:22px}
    .ppdb-liftoff-ui__panel{padding:clamp(20px,3vw,30px);border-radius:24px;background:#fff;box-shadow:0 18px 46px rgba(24,18,41,.16)}
    .ppdb-liftoff-ui__panel h3{margin:0 0 14px;font-size:clamp(1rem,1.4vw,1.2rem);letter-spacing:-.03em}
    .ppdb-liftoff-ui__line{height:12px;border-radius:999px;background:rgba(170,171,250,.28)}
    .ppdb-liftoff-ui__line+.ppdb-liftoff-ui__line{width:72%;margin-top:10px}
    .ppdb-liftoff-list{display:grid;gap:12px}
    .ppdb-liftoff-list__item{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px;border-radius:16px;background:#fff;border:1px solid rgba(24,18,41,.06);box-shadow:0 10px 26px rgba(24,18,41,.07);color:rgba(24,18,41,.72);font-weight:800}
    .ppdb-liftoff-list__item span{width:36px;height:36px;display:grid;place-items:center;border-radius:12px;background:#181229;color:#fff;flex:0 0 auto}
    .ppdb-liftoff-chip{position:absolute;right:8%;bottom:9%;max-width:74%;padding:16px 18px;border-radius:18px;background:rgba(255,255,255,.9);box-shadow:0 16px 44px rgba(24,18,41,.18);color:rgba(24,18,41,.76);font-weight:900;backdrop-filter:blur(10px)}
    .ppdb-liftoff-card__text{justify-self:center;max-width:480px}
    .ppdb-liftoff-step{width:42px;height:42px;display:grid;place-items:center;margin-bottom:22px;border-radius:999px;background:linear-gradient(#fffbf4,#fffbf4) padding-box,linear-gradient(135deg,#cfe6ff,#aaaafa 55%,#fa946c) border-box;border:3px solid transparent;color:#181229;font-weight:950}
    .ppdb-liftoff-panel--school .ppdb-liftoff-step{background:linear-gradient(#181229,#181229) padding-box,linear-gradient(135deg,#a3a0ff,#fff 55%,#fa946c) border-box;color:#fff}
    .ppdb-liftoff-card__text h3{margin:0;font-size:clamp(1.8rem,3.2vw,3.2rem);line-height:1.02;letter-spacing:-.065em;font-weight:950}
    .ppdb-liftoff-card__text p{margin-top:20px;color:rgba(24,18,41,.68);font-size:clamp(1rem,1.35vw,1.16rem);line-height:1.65}
    .ppdb-liftoff-panel--school .ppdb-liftoff-card__text p{color:rgba(255,255,255,.72)}
    .ppdb-liftoff__cta{position:relative;z-index:2;display:grid;justify-items:center;gap:18px;max-width:720px;margin:clamp(70px,8vw,110px) auto 0;text-align:center}
    .ppdb-liftoff__cta p{color:rgba(24,18,41,.68);font-size:1.04rem;line-height:1.7}
    @media (max-width:900px){.ppdb-liftoff::before{inset:16px;border-radius:28px}.ppdb-liftoff__rail{display:none}.ppdb-liftoff-panel{min-height:0}.ppdb-liftoff__stack{gap:64px}.ppdb-liftoff-card,.ppdb-liftoff-card:nth-child(even){grid-template-columns:1fr;min-height:0}.ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__visual,.ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__text{order:initial}.ppdb-liftoff-card__text{max-width:none;justify-self:start}}
    @media (max-width:620px){.ppdb-liftoff{padding-block:64px}.ppdb-liftoff__switch{width:100%}.ppdb-liftoff__tab{min-width:0;padding-inline:12px;font-size:.9rem}.ppdb-liftoff-card__visual{min-height:320px;border-radius:24px}.ppdb-liftoff-ui{inset:9% 7% auto}.ppdb-liftoff-chip{left:7%;right:7%;max-width:none}}
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

  @if ($ppdbAvailableAudiences->isNotEmpty())
    <section class="ppdb-liftoff" data-ppdb-liftoff data-active-audience="{{ $ppdbInitialAudience }}" aria-labelledby="ppdb-journey-title">
      <div class="container">
        <div class="ppdb-liftoff__top reveal">
          <div class="ppdb-liftoff__switch" role="tablist" aria-label="{{ $isEnglish ? 'Admission flow audience' : 'Target alur PPDB' }}">
            @foreach (['parents', 'school'] as $audience)
              @php $hasAudienceItems = $ppdbShowcaseByAudience[$audience]->isNotEmpty(); @endphp
              <button type="button" class="ppdb-liftoff__tab {{ $ppdbInitialAudience === $audience ? 'is-active' : '' }}" data-ppdb-liftoff-tab="{{ $audience }}" role="tab" aria-selected="{{ $ppdbInitialAudience === $audience ? 'true' : 'false' }}" @disabled(! $hasAudienceItems)>{{ $ppdbAudienceLabels[$audience] }}</button>
            @endforeach
          </div>
          <h2 id="ppdb-journey-title">{{ $page['psb_showcase']['title'] }}</h2>
          <p>{{ $page['psb_showcase']['subtitle'] }}</p>
        </div>

        @foreach ($ppdbShowcaseByAudience as $audience => $items)
          @if ($items->isNotEmpty())
            <div class="ppdb-liftoff-panel ppdb-liftoff-panel--{{ $audience }}" data-ppdb-liftoff-panel="{{ $audience }}" @if($ppdbInitialAudience !== $audience) hidden @endif>
              <div class="ppdb-liftoff__rail" aria-hidden="true">
                <svg viewBox="0 0 720 1800" preserveAspectRatio="none">
                  <defs>
                    <filter id="ppdbLiftoffRailGlow" x="-35%" y="-35%" width="170%" height="170%">
                      <feDropShadow dx="0" dy="10" stdDeviation="8" flood-color="{{ $audience === 'school' ? '#a3a0ff' : '#fa946c' }}" flood-opacity=".34" />
                    </filter>
                    <filter id="ppdbLiftoffRailDotGlow" x="-80%" y="-80%" width="260%" height="260%">
                      <feDropShadow dx="0" dy="8" stdDeviation="7" flood-color="{{ $audience === 'school' ? '#a3a0ff' : '#fa946c' }}" flood-opacity=".52" />
                    </filter>
                    <linearGradient id="ppdbLiftoffRailGradient{{ $audience === 'school' ? 'School' : 'Parents' }}" x1="0" x2="1" y1="0" y2="1">
                      @if ($audience === 'school')
                        <stop offset="0%" stop-color="#a3a0ff" /><stop offset="50%" stop-color="#ffffff" /><stop offset="100%" stop-color="#fa946c" />
                      @else
                        <stop offset="0%" stop-color="#d2e7ff" /><stop offset="45%" stop-color="#aaaafa" /><stop offset="100%" stop-color="#fa946c" />
                      @endif
                    </linearGradient>
                  </defs>
                  <path class="ppdb-liftoff__rail-base" d="M 604 10 C 682 170 614 304 438 364 L 106 478 C 14 510 24 650 132 690 L 614 868 C 724 908 714 1072 562 1138 L 126 1326 C 18 1372 54 1534 196 1570 L 508 1650 C 634 1682 646 1750 558 1790" />
                  <path class="ppdb-liftoff__rail-progress" data-ppdb-rail-progress d="M 604 10 C 682 170 614 304 438 364 L 106 478 C 14 510 24 650 132 690 L 614 868 C 724 908 714 1072 562 1138 L 126 1326 C 18 1372 54 1534 196 1570 L 508 1650 C 634 1682 646 1750 558 1790" />
                  <circle class="ppdb-liftoff__rail-dot" data-ppdb-rail-dot r="15" cx="604" cy="10" />
                </svg>
              </div>

              <div class="ppdb-liftoff__stack">
                @foreach($items as $item)
                  @php
                    $itemNumber = str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT);
                    $itemTitle = $item->titleForLocale(app()->getLocale());
                    $itemDescription = $item->descriptionForLocale(app()->getLocale());
                  @endphp
                  <article class="ppdb-liftoff-card reveal">
                    <div class="ppdb-liftoff-card__visual">
                      @if ($item->media_url && $item->is_video)
                        <div class="ppdb-liftoff-media"><iframe src="{{ $item->media_url }}" loading="lazy" allowfullscreen title="{{ $itemTitle }}"></iframe></div>
                      @elseif ($item->media_url)
                        <div class="ppdb-liftoff-media"><img src="{{ $item->media_url }}" alt="{{ $itemTitle }}" loading="lazy"></div>
                      @else
                        <div class="ppdb-liftoff-ui" aria-hidden="true">
                          <div class="ppdb-liftoff-ui__panel"><h3>{{ $itemTitle }}</h3><div class="ppdb-liftoff-ui__line"></div><div class="ppdb-liftoff-ui__line"></div></div>
                          <div class="ppdb-liftoff-ui__panel"><div class="ppdb-liftoff-list"><div class="ppdb-liftoff-list__item"><span>{{ $itemNumber }}</span>{{ $audience === 'school' ? ($isEnglish ? 'School task' : 'Tugas sekolah') : ($isEnglish ? 'Family note' : 'Catatan keluarga') }}</div><div class="ppdb-liftoff-list__item"><span>✓</span>{{ $isEnglish ? 'Clear follow-up' : 'Follow-up jelas' }}</div></div></div>
                        </div>
                      @endif
                      <div class="ppdb-liftoff-chip">{{ $audience === 'school' ? ($isEnglish ? 'Manage admission content without editing code.' : 'Kelola konten PPDB tanpa menyentuh kode.') : ($isEnglish ? 'Admission flow stays clear, warm, and easy to share.' : 'Alur PPDB tetap jelas, hangat, dan mudah dibagikan.') }}</div>
                    </div>
                    <div class="ppdb-liftoff-card__text"><div class="ppdb-liftoff-step">{{ $itemNumber }}</div><h3>{{ $itemTitle }}</h3><p>{{ $itemDescription }}</p></div>
                  </article>
                @endforeach
              </div>
            </div>
          @endif
        @endforeach

        <div class="ppdb-liftoff__cta reveal"><p>{{ $page['psb_showcase']['note'] }}</p><a href="#alur-ppdb" class="btn btn--primary">{{ $page['psb_showcase']['button'] }}</a></div>
      </div>
    </section>

    <script>
      (() => {
        const root = document.querySelector('[data-ppdb-liftoff]');
        if (!root) return;

        const tabs = Array.from(root.querySelectorAll('[data-ppdb-liftoff-tab]'));
        const panels = Array.from(root.querySelectorAll('[data-ppdb-liftoff-panel]'));
        const progressPaths = Array.from(root.querySelectorAll('[data-ppdb-rail-progress]'));
        const liftoffReveals = Array.from(root.querySelectorAll('.reveal'));
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        progressPaths.forEach((path) => {
          const length = path.getTotalLength();
          path.dataset.length = String(length);
          path.style.strokeDasharray = String(length);
          path.style.strokeDashoffset = String(length);

          const dot = path.parentElement.querySelector('[data-ppdb-rail-dot]');
          if (dot) {
            const point = path.getPointAtLength(0);
            dot.dataset.progressPathLength = String(length);
            dot.setAttribute('cx', String(point.x));
            dot.setAttribute('cy', String(point.y));
            dot.style.opacity = '0';
          }
        });

        const revealVisibleItems = (scope = root) => {
          Array.from(scope.querySelectorAll('.reveal')).forEach((element) => {
            const rect = element.getBoundingClientRect();
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

            if (rect.top < viewportHeight * 1.08 && rect.bottom > -viewportHeight * 0.18) {
              element.classList.add('is-visible');
            }
          });
        };

        if ('IntersectionObserver' in window && liftoffReveals.length) {
          const earlyRevealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
              if (!entry.isIntersecting) return;

              entry.target.classList.add('is-visible');
              earlyRevealObserver.unobserve(entry.target);
            });
          }, {
            root: null,
            rootMargin: '44% 0px 10% 0px',
            threshold: 0.01,
          });

          liftoffReveals.forEach((element) => earlyRevealObserver.observe(element));
        }

        const updateRail = () => {
          if (prefersReducedMotion) {
            progressPaths.forEach((path) => {
              path.style.strokeDashoffset = '0';

              const dot = path.parentElement.querySelector('[data-ppdb-rail-dot]');
              if (!dot) return;

              const point = path.getPointAtLength(path.getTotalLength());
              dot.setAttribute('cx', String(point.x));
              dot.setAttribute('cy', String(point.y));
              dot.style.opacity = '1';
            });
            return;
          }

          const activePanel = root.querySelector(`[data-ppdb-liftoff-panel="${root.dataset.activeAudience}"]`);
          if (!activePanel || activePanel.hidden) return;

          const path = activePanel.querySelector('[data-ppdb-rail-progress]');
          if (!path) return;

          const length = Number(path.dataset.length || path.getTotalLength());
          const rect = activePanel.getBoundingClientRect();
          const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
          const start = (viewportHeight * 0.76) - rect.top;
          const end = Math.max(activePanel.offsetHeight - (viewportHeight * 0.22), 1);
          const current = Math.min(Math.max(start, 0), end);
          const progress = Math.min(Math.max(current / end, 0), 1);
          const drawnLength = length * progress;
          const dot = path.parentElement.querySelector('[data-ppdb-rail-dot]');

          path.style.strokeDashoffset = String(length - drawnLength);

          if (dot) {
            const point = path.getPointAtLength(Math.min(Math.max(drawnLength, 0), length));
            dot.setAttribute('cx', String(point.x));
            dot.setAttribute('cy', String(point.y));
            dot.style.opacity = progress > 0.015 ? '1' : '0';
          }

          revealVisibleItems(activePanel);
        };

        const activate = (audience) => {
          const targetPanel = root.querySelector(`[data-ppdb-liftoff-panel="${audience}"]`);
          if (!targetPanel) return;

          root.dataset.activeAudience = audience;
          tabs.forEach((tab) => {
            const isActive = tab.dataset.ppdbLiftoffTab === audience;
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
          });
          panels.forEach((panel) => { panel.hidden = panel.dataset.ppdbLiftoffPanel !== audience; });
          window.requestAnimationFrame(() => {
            revealVisibleItems(targetPanel);
            updateRail();
          });
        };

        tabs.forEach((tab) => tab.addEventListener('click', () => {
          if (!tab.disabled) activate(tab.dataset.ppdbLiftoffTab);
        }));

        let ticking = false;
        const queueUpdate = () => {
          if (ticking) return;
          ticking = true;
          window.requestAnimationFrame(() => {
            revealVisibleItems();
            updateRail();
            ticking = false;
          });
        };

        window.addEventListener('scroll', queueUpdate, { passive: true });
        window.addEventListener('resize', queueUpdate);
        window.requestAnimationFrame(() => {
          revealVisibleItems();
          updateRail();
        });
      })();
    </script>
  @endif

  <section id="alur-ppdb" class="public-section"><div class="container"><div class="public-section-head"><h2>{{ $page['steps_intro']['heading'] }}</h2><p>{{ $page['steps_intro']['subtitle'] }}</p></div><div class="public-step-grid">@foreach($page['steps'] as $index => $step)<article class="public-step-card reveal"><span class="public-step-card__number">{{ sprintf('%02d', $index + 1) }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></article>@endforeach</div></div></section>
  <section class="public-section public-section--soft"><div class="container"><div class="public-section-head"><h2>{{ $page['program_intro']['heading'] }}</h2></div><div class="program-public-grid">@foreach($page['programs'] as $program)<article class="program-public-card reveal"><span class="program-public-card__icon" aria-hidden="true">{{ $program['icon'] }}</span><p class="program-public-card__age">{{ $program['age'] }}</p><h3>{{ $program['name'] }}</h3><p>{{ $program['text'] }}</p></article>@endforeach</div></div></section>
  <section class="public-section"><div class="container ppdb-info-grid"><div><div class="public-section-head public-section-head--compact"><h2>{{ $page['documents_intro']['heading'] }}</h2></div><ul class="document-list">@foreach($page['documents'] as $document)<li class="reveal">{{ $document }}</li>@endforeach</ul></div><div><div class="public-section-head public-section-head--compact"><h2>{{ $page['timeline_intro']['heading'] }}</h2></div><div class="timeline-list">@foreach($page['timeline'] as $item)<article class="timeline-card reveal"><span>{{ $item['date'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>@endforeach</div></div></div></section>
  <section class="public-section public-section--soft"><div class="container"><div class="public-section-head"><h2>{{ $page['faq_intro']['heading'] }}</h2></div><div class="faq-grid">@foreach($page['faq'] as $faq)<details class="faq-card reveal"><summary>{{ $faq['q'] }}</summary><p>{{ $faq['a'] }}</p></details>@endforeach</div></div></section>
  <section class="public-final-cta"><div class="container"><div class="public-final-cta__box reveal"><span aria-hidden="true">📮</span><h2>{{ $page['final_cta']['heading'] }}</h2><p>{{ $page['final_cta']['subtitle'] }}</p>@if ($ppdbIsOpen && $ppdbFormUrl)<a href="{{ $ppdbFormUrl }}" class="btn btn--white" target="_blank" rel="noopener noreferrer">{{ $ppdbFinalButtonLabel }}</a>@else<a href="#ppdb-closed-modal" class="btn btn--white">{{ $ppdbFinalButtonLabel }}</a>@endif</div></div></section>
  <section class="ppdb-closed-modal" id="ppdb-closed-modal" role="dialog" aria-modal="true" aria-labelledby="ppdb-closed-title" aria-describedby="ppdb-closed-description"><a href="#" class="ppdb-closed-modal__backdrop" aria-label="{{ __('pages.common.close') }}"></a><div class="ppdb-closed-modal__panel"><div class="ppdb-closed-modal__icon" aria-hidden="true">🕊️</div><h2 id="ppdb-closed-title">{{ $ppdbClosedTitle }}</h2><p id="ppdb-closed-description">{{ $ppdbClosedText }}</p><a href="#" class="ppdb-closed-modal__close">{{ $ppdbClosedButton }}</a></div></section>
@endsection
