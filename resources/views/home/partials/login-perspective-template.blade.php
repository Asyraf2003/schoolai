@php
    $perspectiveRoleLabels = match (app()->getLocale()) {
        'en' => [
            'admin' => 'Login as Admin',
            'guru' => 'Login as Teacher',
            'murid' => 'Login as Student',
        ],
        'ar' => [
            'admin' => 'تسجيل الدخول كمسؤول',
            'guru' => 'تسجيل الدخول كمعلم',
            'murid' => 'تسجيل الدخول كطالب',
        ],
        default => [
            'admin' => 'Login sebagai Admin',
            'guru' => 'Login sebagai Guru',
            'murid' => 'Login sebagai Murid',
        ],
    };
@endphp

<template data-login-perspective-template>
    <aside
        id="loginPerspectivePanel"
        class="login-perspective__panel outer-nav left vertical"
        data-login-perspective-panel
        aria-label="{{ __('app.auth.portal.choices_label') }}"
    >
        <section class="login-perspective__state" data-login-perspective-state="choices">
            <p class="login-perspective__eyebrow">LOGIN</p>
            <h2>{{ __('app.auth.portal.heading') }}</h2>
            <p class="login-perspective__description">{{ __('app.auth.portal.description') }}</p>

            <div class="login-perspective__choices">
                <button type="button" data-login-role-target="admin">
                    {{ $perspectiveRoleLabels['admin'] }}
                </button>
                <button type="button" data-login-role-target="guru">
                    {{ $perspectiveRoleLabels['guru'] }}
                </button>
                <button type="button" data-login-role-target="murid">
                    {{ $perspectiveRoleLabels['murid'] }}
                </button>
            </div>
        </section>

        <section class="login-perspective__state" data-login-perspective-state="admin" data-auth-scope hidden>
            <p class="login-perspective__eyebrow">ADMIN</p>
            <h2>{{ $perspectiveRoleLabels['admin'] }}</h2>
            <p class="login-perspective__description">{{ __('app.auth.login.description') }}</p>
            <div class="login-perspective__message" data-auth-message role="alert"></div>

            <a
                class="login-perspective__primary"
                href="{{ route('google.redirect') }}"
                data-google-login
                data-google-popup="1"
                data-auth-role="admin"
                data-loading-label="{{ __('app.auth.guru_login.loading') }}"
            >
                <span>{{ __('app.auth.login.google_button') }}</span>
            </a>

            <button class="login-perspective__back" type="button" data-login-perspective-back>
                {{ __('app.auth.portal.back_choices') }}
            </button>
        </section>

        <section class="login-perspective__state" data-login-perspective-state="guru" data-auth-scope hidden>
            <p class="login-perspective__eyebrow">GURU</p>
            <h2>{{ $perspectiveRoleLabels['guru'] }}</h2>
            <p class="login-perspective__description">{{ __('app.auth.guru_login.description') }}</p>
            <div class="login-perspective__message" data-auth-message role="alert"></div>

            <a
                class="login-perspective__primary"
                href="{{ route('google.guru.redirect') }}"
                data-google-login
                data-google-popup="1"
                data-auth-role="guru"
                data-loading-label="{{ __('app.auth.guru_login.loading') }}"
            >
                <span>{{ __('app.auth.guru_login.google_button') }}</span>
            </a>

            <button class="login-perspective__back" type="button" data-login-perspective-back>
                {{ __('app.auth.portal.back_choices') }}
            </button>
        </section>

        <section class="login-perspective__state" data-login-perspective-state="murid" data-auth-scope hidden>
            <p class="login-perspective__eyebrow">MURID</p>
            <h2>{{ $perspectiveRoleLabels['murid'] }}</h2>
            <p class="login-perspective__description">{{ __('app.auth.student_login.description') }}</p>
            <div class="login-perspective__message" data-auth-message role="alert"></div>

            <form
                class="login-perspective__form"
                method="POST"
                action="{{ route('murid.login.store') }}"
                data-async-auth-form
                data-retry-after="0"
                data-success-message="{{ __('app.auth.success.logged_in') }}"
                data-failure-message="{{ __('app.auth.errors.login_failed') }}"
                data-network-message="{{ __('app.auth.errors.network') }}"
            >
                @csrf
                <label>
                    <span>{{ __('app.auth.student_login.student_id') }}</span>
                    <input
                        name="student_id"
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

            <button class="login-perspective__back" type="button" data-login-perspective-back>
                {{ __('app.auth.portal.back_choices') }}
            </button>
        </section>
    </aside>
</template>
