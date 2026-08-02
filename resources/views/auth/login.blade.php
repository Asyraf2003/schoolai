<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.login.title') }}</title>
    @vite([
        'resources/css/app.css',
        'resources/css/pages/public-auth.css',
        'resources/css/text-system.css',
        'resources/css/arabic-typography.css',
        'resources/css/public-latin-inter.css',
        'resources/js/pages/public-login.js',
    ])
</head>
<body class="auth-login-page public-page-body">
    <main class="auth-login" aria-labelledby="login-heading">
        <section class="auth-login__card">
            <a class="auth-login__back" href="{{ route('home') }}">
                {{ __('app.auth.login.back_home') }}
            </a>
            <h1 id="login-heading">{{ __('app.auth.login.heading') }}</h1>
            <p class="auth-login__description">{{ __('app.auth.login.description') }}</p>

            @if (session('success'))
                <p class="auth-login__flash" role="status">{{ session('success') }}</p>
            @endif
            @error('email')
                <p class="auth-login__error" role="alert">{{ $message }}</p>
            @enderror

            <a
                class="auth-login__google"
                href="{{ route('google.redirect') }}"
                data-google-login
                data-loading-label="{{ __('app.auth.guru_login.loading') }}"
            >
                <span>{{ __('app.auth.login.google_button') }}</span>
            </a>
        </section>
    </main>
</body>
</html>
