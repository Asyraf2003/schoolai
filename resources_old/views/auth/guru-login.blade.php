<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.guru_login.title') }}</title>
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
            <a class="auth-login__back" href="{{ route('portal.login') }}">
                {{ __('app.auth.portal.back_choices') }}
            </a>
            <h1 id="login-heading">{{ __('app.auth.guru_login.heading') }}</h1>
            <p class="auth-login__description">
                {{ __('app.auth.guru_login.description') }}
            </p>

            @error('email')
                <p class="auth-login__error" role="alert">{{ $message }}</p>
            @enderror

            <a
                class="auth-login__google"
                href="{{ route('google.guru.redirect') }}"
                data-google-login
                data-loading-label="{{ __('app.auth.guru_login.loading') }}"
            >
                <span>{{ __('app.auth.guru_login.google_button') }}</span>
            </a>
        </section>
    </main>
</body>
</html>
