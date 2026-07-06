<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.login.title') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-login-page" data-page="authLogin">
    <main class="auth-login" aria-labelledby="login-heading">
        <section class="auth-login__shell">
            <div class="auth-login__hero" aria-hidden="true">
                <div class="auth-login__blob auth-login__blob--one"></div>
                <div class="auth-login__blob auth-login__blob--two"></div>

                <div class="auth-login__brand-card">
                    <span class="auth-login__brand-mark">✦</span>
                    <span class="auth-login__brand-title">{{ config('app.name', 'School AI') }}</span>
                    <span class="auth-login__brand-subtitle">{{ __('app.auth.login.heading') }}</span>
                </div>

                <div class="auth-login__floating-card auth-login__floating-card--top">
                    <span>🔐</span>
                    <strong>{{ __('app.auth.login.email_label') }}</strong>
                </div>

                <div class="auth-login__floating-card auth-login__floating-card--bottom">
                    <span>✨</span>
                    <strong>{{ __('app.auth.login.google_button') }}</strong>
                </div>
            </div>

            <section class="auth-login__panel" aria-labelledby="login-heading">
                <div class="auth-login__header">
                    <p class="auth-login__kicker">{{ config('app.name', 'School AI') }}</p>
                    <h1 id="login-heading">{{ __('app.auth.login.heading') }}</h1>
                </div>

                @if (session('success'))
                    <p class="auth-login__flash" role="status">{{ session('success') }}</p>
                @endif

                <a class="auth-login__google" href="{{ route('google.redirect') }}">
                    <span class="auth-login__google-icon" aria-hidden="true">
                        <img
                            src="{{ asset('media/home/search.png') }}"
                            alt=""
                            width="22"
                            height="22"
                            loading="eager"
                            decoding="async"
                        >
                    </span>
                    <span>{{ __('app.auth.login.google_button') }}</span>
                </a>

                <div class="auth-login__separator" aria-hidden="true">
                    <span></span>
                    <strong>{{ __('app.auth.login.separator') }}</strong>
                    <span></span>
                </div>

                <form class="auth-login__form" method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="auth-login__field">
                        <label for="email">{{ __('app.auth.login.email_label') }}</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        >

                        @error('email')
                            <p class="auth-login__error" id="email-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="auth-login__field">
                        <label for="password">{{ __('app.auth.login.password_label') }}</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        >

                        @error('password')
                            <p class="auth-login__error" id="password-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="auth-login__remember">
                        <input type="checkbox" name="remember" value="1">
                        <span>{{ __('app.auth.login.remember_label') }}</span>
                    </label>

                    <button class="auth-login__submit" type="submit">
                        {{ __('app.auth.login.submit') }}
                    </button>
                </form>
            </section>
        </section>
    </main>
</body>
</html>
