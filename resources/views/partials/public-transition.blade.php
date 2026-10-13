<div class="public-transition" data-public-transition aria-hidden="true">
    @for($slice = 0; $slice < 10; $slice++)
        <span class="public-transition__slice"><i></i><b></b></span>
    @endfor
    <img class="public-transition__logo" src="{{ $transitionLogo ?? config('media.static.brand.logo_nav') }}" alt="" width="96" height="96">
</div>
