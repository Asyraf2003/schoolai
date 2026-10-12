@extends('portal.shell')

@section('title', __('app.auth.login.title'))

@section('content')
    <section data-auth-entry>
        <p class="eyebrow">Al Mustaqbal School</p>
        @if ($activeRole === null)
            <h1>{{ __('app.auth.portal.heading') }}</h1>
            <p>{{ __('app.auth.portal.description') }}</p>
            <nav class="choices" aria-label="{{ __('app.auth.portal.choices_label') }}">
                <a href="{{ route('login') }}">{{ \App\Enums\AccountRole::Admin->label() }}</a>
                <a href="{{ route('guru.login') }}">{{ __('app.auth.navigation.guru') }}</a>
                <a href="{{ route('murid.login') }}">{{ __('app.auth.navigation.murid') }}</a>
            </nav>
        @elseif ($activeRole === 'admin' || $activeRole === 'guru')
            @php($isTeacher = $activeRole === 'guru')
            <h1>{{ $isTeacher ? __('app.auth.guru_login.heading') : __('app.auth.login.heading') }}</h1>
            <p>{{ $isTeacher ? __('app.auth.guru_login.description') : __('app.auth.login.description') }}</p>
            @if ($errors->any())
                <p class="error" role="alert">{{ $errors->first() }}</p>
            @endif
            <div class="actions">
                <a class="action" href="{{ $isTeacher ? route('google.guru.redirect') : route('google.redirect') }}">{{ $isTeacher ? __('app.auth.guru_login.google_button') : __('app.auth.login.google_button') }}</a>
                <a class="action secondary" href="{{ route('portal.login') }}">{{ __('app.auth.portal.back_choices') }}</a>
            </div>
        @else
            <h1>{{ __('app.auth.student_login.heading') }}</h1>
            <p>{{ __('app.auth.student_login.description') }}</p>
            @if ($errors->any())
                <p class="error" role="alert">{{ $errors->first() }}</p>
            @endif
            <form class="fields" action="{{ route('murid.login.store') }}" method="POST">
                @csrf
                <label>{{ __('app.auth.student_login.student_id') }}
                    <input type="text" name="student_id" value="{{ old('student_id') }}" autocomplete="username" required maxlength="32" pattern="[A-Za-z0-9]{1,32}">
                </label>
                <label>{{ __('app.auth.student_login.password') }}
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>
                <button type="submit">{{ __('app.auth.student_login.submit') }}</button>
            </form>
            <div class="actions">
                <a class="action secondary" href="{{ route('portal.login') }}">{{ __('app.auth.portal.back_choices') }}</a>
            </div>
        @endif
    </section>
@endsection
