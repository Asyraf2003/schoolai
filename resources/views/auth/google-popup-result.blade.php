<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.portal.title') }}</title>
</head>
<body>
    <p>{{ $message }}</p>
    <p><a href="{{ $fallbackUrl }}">{{ __('app.auth.portal.back_choices') }}</a></p>

    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        (() => {
            const payload = @json([
                'source' => 'schoolai-google-auth',
                'ok' => $ok,
                'role' => $role,
                'redirect' => $redirect,
                'message' => $message,
            ]);
            const fallbackUrl = @json($fallbackUrl);

            if (window.opener && !window.opener.closed) {
                window.opener.postMessage(payload, window.location.origin);
                window.close();
                return;
            }

            if (payload.ok && payload.redirect) {
                window.location.replace(payload.redirect);
                return;
            }

            window.location.replace(fallbackUrl);
        })();
    </script>
</body>
</html>
