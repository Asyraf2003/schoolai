@php
    $currentRole = in_array($activeRole ?? null, ['admin', 'guru', 'murid'], true)
        ? $activeRole
        : null;
    $roleLabels = match (app()->getLocale()) {
        'en' => ['admin' => 'Login as Admin', 'guru' => 'Login as Teacher', 'murid' => 'Login as Student'],
        'ar' => ['admin' => 'تسجيل الدخول كمسؤول', 'guru' => 'تسجيل الدخول كمعلم', 'murid' => 'تسجيل الدخول كطالب'],
        default => ['admin' => 'Login sebagai Admin', 'guru' => 'Login sebagai Guru', 'murid' => 'Login sebagai Murid'],
    };
@endphp

<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.portal.title') }}</title>
    @vite([
        'resources/css/app.css',
        'resources/css/pages/public-auth-perspective.css',
        'resources/css/text-system.css',
        'resources/css/arabic-typography.css',
        'resources/css/public-latin-inter.css',
        'resources/js/pages/public-login.js',
        'resources/js/pages/public-auth-perspective.js',
    ])
</head>
<body class="auth-perspective-page public-page-body">
    <div
        id="auth-perspective"
        class="auth-perspective effect-airbnb"
        data-auth-perspective
        data-auth-auto-open="{{ $currentRole === null ? '1' : '0' }}"
    >
        <div class="auth-perspective__container" data-auth-perspective-container>
            <div class="auth-perspective__wrapper" data-auth-perspective-wrapper>
                <main class="auth-login" aria-live="polite">
                    <a class="auth-login__home" href="{{ route('home') }}">
                        {{ __('app.auth.login.back_home') }}
                    </a>

                    @if ($currentRole === null)
                        <section class="auth-login__panel" data-auth-role-panel="intro">
                            <p class="auth-login__eyebrow">LOGIN</p>
                            <h1>{{ __('app.auth.portal.heading') }}</h1>
                            <p class="auth-login__description">{{ __('app.auth.portal.description') }}</p>
                            <button class="auth-login__menu-button" type="button" data-auth-perspective-open>
                                {{ __('app.auth.portal.choices_label') }}
                            </button>
                        </section>
                    @elseif ($currentRole === 'admin')
                        <section class="auth-login__panel" data-auth-role-panel="admin">
                            <p class="auth-login__eyebrow">ADMIN</p>
                            <h1>{{ __('app.auth.login.heading') }}</h1>
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

                            <a class="auth-login__switch" href="{{ route('portal.login') }}" data-auth-perspective-open>
                                {{ __('app.auth.portal.back_choices') }}
                            </a>
                        </section>
                    @elseif ($currentRole === 'guru')
                        <section class="auth-login__panel" data-auth-role-panel="guru">
                            <p class="auth-login__eyebrow">GURU</p>
                            <h1>{{ __('app.auth.guru_login.heading') }}</h1>
                            <p class="auth-login__description">{{ __('app.auth.guru_login.description') }}</p>

                            @if (session('success'))
                                <p class="auth-login__flash" role="status">{{ session('success') }}</p>
                            @endif
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

                            <a class="auth-login__switch" href="{{ route('portal.login') }}" data-auth-perspective-open>
                                {{ __('app.auth.portal.back_choices') }}
                            </a>
                        </section>
                    @else
                        <section class="auth-login__panel" data-auth-role-panel="murid">
                            <p class="auth-login__eyebrow">MURID</p>
                            <h1>{{ __('app.auth.student_login.heading') }}</h1>
                            <p class="auth-login__description">{{ __('app.auth.student_login.description') }}</p>

                            @if (session('success'))
                                <p class="auth-login__flash" role="status">{{ session('success') }}</p>
                            @endif

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

                            <a class="auth-login__switch" href="{{ route('portal.login') }}" data-auth-perspective-open>
                                {{ __('app.auth.portal.back_choices') }}
                            </a>
                        </section>
                    @endif
                </main>
            </div>
        </div>

        <nav class="auth-perspective__nav outer-nav left vertical" aria-label="{{ __('app.auth.portal.choices_label') }}">
            <a href="{{ route('login') }}" data-auth-role-target="admin">{{ $roleLabels['admin'] }}</a>
            <a href="{{ route('guru.login') }}" data-auth-role-target="guru">{{ $roleLabels['guru'] }}</a>
            <a href="{{ route('murid.login') }}" data-auth-role-target="murid">{{ $roleLabels['murid'] }}</a>
        </nav>
    </div>
</body>
</html>
