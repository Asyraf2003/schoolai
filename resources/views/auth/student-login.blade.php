<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.student_login.title') }}</title>
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
    <main class="auth-login" aria-labelledby="student-login-title">
        <section class="auth-login__card">
            <a class="auth-login__back" href="{{ route('home') }}">
                {{ __('app.auth.login.back_home') }}
            </a>
            <h1 id="student-login-title">{{ __('app.auth.student_login.heading') }}</h1>
            <p class="auth-login__description">{{ __('app.auth.student_login.description') }}</p>

            <div class="auth-login__message" data-auth-message role="alert">
                @error('credentials'){{ $message }}@enderror
            </div>

            <form
                class="auth-login__form"
                method="POST"
                action="{{ route('murid.login.store') }}"
                data-async-auth-form
                data-retry-after="{{ (int) session('retry_after', 0) }}"
                data-failure-message="{{ __('app.auth.errors.login_failed') }}"
                data-network-message="{{ __('app.auth.errors.network') }}"
            >
                @csrf
                <label>
                    <span>{{ __('app.auth.student_login.student_id') }}</span>
                    <input
                        name="student_id"
                        value="{{ old('student_id') }}"
                        required
                        maxlength="32"
                        pattern="[A-Za-z0-9]{1,32}"
                        autocomplete="username"
                    >
                </label>
                <label>
                    <span>{{ __('app.auth.student_login.password') }}</span>
                    <input name="password" type="password" required autocomplete="current-password">
                </label>
                <button
                    type="submit"
                    data-idle-label="{{ __('app.auth.student_login.submit') }}"
                    data-loading-label="{{ __('app.auth.student_login.loading') }}"
                    data-locked-label="{{ __('app.auth.student_login.locked_countdown') }}"
                >{{ __('app.auth.student_login.submit') }}</button>
            </form>
        </section>
    </main>
</body>
</html>
