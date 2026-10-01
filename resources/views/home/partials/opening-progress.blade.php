<div class="home-opening" data-home-opening>
  <div class="home-opening__status" data-text-role="meta">
    <span id="home-opening-label">{{ __('home.opening.preparing') }}</span>
    <span data-home-opening-percent aria-hidden="true">0%</span>
  </div>
  <progress class="home-opening__progress" data-home-opening-progress value="0" max="100"
    aria-labelledby="home-opening-label"></progress>
  <a class="home-opening__exit" data-home-opening-exit href="#main-content" hidden>{{ __('home.opening.continue') }}</a>
</div>
