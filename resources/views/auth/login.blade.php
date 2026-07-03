<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.login.title') }}</title>
</head>
<body>
    <main>
        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <h1>{{ __('app.auth.login.heading') }}</h1>

        <p>
            <a href="{{ route('google.redirect') }}">{{ __('app.auth.login.google_button') }}</a>
        </p>

        <p>{{ __('app.auth.login.separator') }}</p>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label for="email">{{ __('app.auth.login.email_label') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >

                @error('email')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password">{{ __('app.auth.login.password_label') }}</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                >

                @error('password')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <label>
                <input type="checkbox" name="remember" value="1">
                {{ __('app.auth.login.remember_label') }}
            </label>

            <button type="submit">{{ __('app.auth.login.submit') }}</button>
        </form>
    </main>
</body>
</html>
