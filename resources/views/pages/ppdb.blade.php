@extends('portal.shell')

@section('title', __('pages.ppdb.title'))

@section('content')
    <section data-ppdb-page>
        <p class="eyebrow">Al Mustaqbal School</p>
        <h1>{{ __('pages.ppdb.title') }}</h1>
        <p>{{ __('portal.ppdb.description') }}</p>
        <div class="actions">
            @if ($ppdbAdmission->publicRegistrationUrl())
                <a class="action" href="{{ $ppdbAdmission->publicRegistrationUrl() }}" rel="noopener noreferrer" target="_blank">{{ __('portal.ppdb.apply') }}</a>
            @endif
            @if ($ppdbAdmission->publicInformationUrl())
                <a class="action secondary" href="{{ $ppdbAdmission->publicInformationUrl() }}" rel="noopener noreferrer" target="_blank">{{ __('portal.ppdb.information') }}</a>
            @endif
            <a class="action secondary" href="{{ route('home') }}">{{ __('app.auth.login.back_home') }}</a>
        </div>
    </section>
@endsection
