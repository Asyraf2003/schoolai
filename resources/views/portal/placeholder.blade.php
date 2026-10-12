@extends('portal.shell')

@section('title', __($page['titleKey']))

@section('content')
    <section data-portal-page="{{ $page['id'] }}">
        <p class="eyebrow">Al Mustaqbal School</p>
        <h1>{{ __($page['titleKey']) }}</h1>
        <p>{{ __($page['descriptionKey']) }}</p>
        <div class="actions">
            <a class="action secondary" href="{{ route('home') }}">{{ __('app.auth.login.back_home') }}</a>
        </div>
    </section>
@endsection
