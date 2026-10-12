<nav class="public-nav" aria-label="{{ __('pages.common.main_nav') }}">
    @foreach(['home', 'galeri', 'artikel'] as $page)
        <a href="{{ route($page) }}" @if(request()->routeIs($page, $page.'.*')) aria-current="page" @endif>{{ __('pages.common.nav.'.$page) }}</a>
    @endforeach
    @if($ppdbUrl)<a href="{{ $ppdbUrl }}">{{ __('pages.common.nav.ppdb') }}</a>@endif
</nav>
<div class="public-languages" role="group" aria-label="{{ __('pages.common.language_label') }}">
    @foreach(['id' => 'Indonesia', 'en' => 'English', 'ar' => 'العربية'] as $locale => $name)
        <form method="POST" action="{{ route('language.switch', $locale) }}">
            @csrf
            <button class="secondary" type="submit" lang="{{ $locale }}" @if(app()->getLocale() === $locale) aria-current="true" @endif>{{ $name }}</button>
        </form>
    @endforeach
</div>
