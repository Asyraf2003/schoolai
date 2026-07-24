    <section class="ppdb-liftoff" data-ppdb-liftoff data-active-audience="{{ $ppdbInitialAudience }}" aria-labelledby="ppdb-journey-title">
      <div class="container">
        <div class="ppdb-liftoff__top reveal">
          <div class="ppdb-liftoff__switch" role="tablist" aria-label="{{ $ppdbAudienceAriaLabel }}">
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
              <div class="ppdb-liftoff__storyline" data-ppdb-storyline aria-hidden="true"></div>

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
                        <div class="ppdb-liftoff-media ppdb-liftoff-media--video">
                          <iframe
                            src="{{ $item->media_url }}"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            referrerpolicy="strict-origin-when-cross-origin"
                            title="{{ $itemTitle }}"
                          ></iframe>
                        </div>
                      @elseif ($item->media_url)
                        <div class="ppdb-liftoff-media ppdb-liftoff-media--image"><img src="{{ $item->media_url }}" alt="{{ $itemTitle }}" loading="lazy" decoding="async"></div>
                      @else
                        <div class="ppdb-liftoff-ui" aria-hidden="true">
                          <div class="ppdb-liftoff-ui__panel"><h3>{{ $itemTitle }}</h3><div class="ppdb-liftoff-ui__line"></div><div class="ppdb-liftoff-ui__line"></div></div>
                          <div class="ppdb-liftoff-ui__panel"><div class="ppdb-liftoff-list"><div class="ppdb-liftoff-list__item"><span>{{ $itemNumber }}</span>{{ $audience === 'school' ? $ppdbSchoolTask : $ppdbFamilyNote }}</div><div class="ppdb-liftoff-list__item"><span>✓</span>{{ $ppdbClearFollowUp }}</div></div></div>
                        </div>
                      @endif
                    </div>
                    <div class="ppdb-liftoff-card__text" data-ppdb-story-node><div class="ppdb-liftoff-step">{{ $itemNumber }}</div><h3>{{ $itemTitle }}</h3><p>{{ $itemDescription }}</p></div>
                  </article>
                @endforeach
              </div>
            </div>
          @endif
        @endforeach

        <div class="ppdb-liftoff__cta reveal"><p>{{ $page['psb_showcase']['note'] }}</p><a href="#alur-ppdb" class="btn btn--primary">{{ $page['psb_showcase']['button'] }}</a></div>
      </div>
    </section>
