<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('home.meta.title') }}</title>
    <meta name="description" content="{{ __('home.meta.description') }}">
    <link rel="icon" href="{{ config('media.static.brand.favicon') }}">
    @vite(['resources/css/index.css', 'resources/js/index.js'])
</head>
<body class="landing">
    <a class="skip-link" href="#main-content">{{ __('home.accessibility.skip_to_content') }}</a>
    @include('landing.header', $navigation)
    <main id="main-content" tabindex="-1">
        @include('landing.hero', ['hero' => $hero])
    </main>
</body>
</html>
