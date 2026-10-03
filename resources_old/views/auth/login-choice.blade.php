<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.portal.title') }}</title>
    @vite([
        'resources/css/app.css',
        'resources/css/pages/public-auth.css',
        'resources/css/text-system.css',
        'resources/css/arabic-typography.css',
        'resources/css/public-latin-inter.css',
    ])
</head>
<body class="auth-login-page public-page-body">
    <main class="auth-login" aria-labelledby="login-choice-heading">
        <section class="auth-login__card">
            <a class="auth-login__back" href="{{ route('home') }}">
                {{ __('app.auth.login.back_home') }}
            </a>

            <h1 id="login-choice-heading">{{ __('app.auth.portal.heading') }}</h1>
            <p class="auth-login__description">{{ __('app.auth.portal.description') }}</p>

            <div class="auth-login__choices" aria-label="{{ __('app.auth.portal.choices_label') }}">
                <a class="auth-login__choice" href="{{ route('guru.login') }}">
                    <strong>{{ __('app.auth.navigation.guru') }}</strong>
                    <span>{{ __('app.auth.navigation.guru_description') }}</span>
                </a>

                <a class="auth-login__choice" href="{{ route('murid.login') }}">
                    <strong>{{ __('app.auth.navigation.murid') }}</strong>
                    <span>{{ __('app.auth.navigation.murid_description') }}</span>
                </a>
            </div>
        </section>
    </main>
</body>
</html>
