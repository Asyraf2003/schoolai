<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>{{ __('app.auth.login.title') }}</title>

    @vite(['resources/css/app.css'])
</head>

<body class="auth-login-page">
    <main
        class="auth-login"
        aria-labelledby="login-heading"
    >
        <section class="auth-login__card">
            <h1 id="login-heading">
                {{ __('app.auth.login.heading') }}
            </h1>

            <p class="auth-login__description">
                {{ __('app.auth.login.description') }}
            </p>

            @if (session('success'))
                <p
                    class="auth-login__flash"
                    role="status"
                >
                    {{ session('success') }}
                </p>
            @endif

            @error('email')
                <p
                    class="auth-login__error"
                    role="alert"
                >
                    {{ $message }}
                </p>
            @enderror

            <a
                class="auth-login__google"
                href="{{ route('google.redirect') }}"
            >
                <img
                    src="{{ asset('media/home/search.png') }}"
                    alt=""
                    width="20"
                    height="20"
                    loading="eager"
                    decoding="async"
                    aria-hidden="true"
                >

                <span>
                    {{ __('app.auth.login.google_button') }}
                </span>
            </a>
        </section>
    </main>
</body>
</html>
