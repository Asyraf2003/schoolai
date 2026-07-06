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
        <section class="auth-login__card">
            <h1 id="login-heading">{{ __('app.auth.login.heading') }}</h1>

            @if (session('success'))
                <p class="auth-login__flash" role="status">{{ session('success') }}</p>
            @endif

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

            <a class="auth-login__google" href="{{ route('google.redirect') }}">
                <img
                    src="{{ asset('media/home/search.png') }}"
                    alt=""
                    width="20"
                    height="20"
                    loading="eager"
                    decoding="async"
                    aria-hidden="true"
                >
                <span>{{ __('app.auth.login.google_button') }}</span>
            </a>
        </section>
    </main>
</body>
</html>
