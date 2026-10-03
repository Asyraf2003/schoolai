<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ __('app.auth.account.title') }}</title>

  @vite([
    'resources/css/app.css',
    'resources/css/pages/account-locked.css',
    'resources/js/app.js',
  ])
</head>

<body class="account-locked-page">
  <main
    class="account-locked-card"
    aria-labelledby="account-locked-heading"
  >
    <div class="account-locked-mark" aria-hidden="true">
      🔒
    </div>

    <p class="account-locked-kicker">
      {{ __('app.auth.account.status') }}
    </p>

    <h1 id="account-locked-heading">
      {{ __('app.auth.account.heading') }}
    </h1>

    <p class="account-locked-description">
      {{ __('app.auth.account.description') }}
    </p>

    <div class="account-locked-user">
      <strong>{{ auth()->user()->name }}</strong>
      <span>{{ auth()->user()->email }}</span>
    </div>

    <form
      class="account-locked-actions"
      method="POST"
      action="{{ route('logout') }}"
    >
      @csrf

      <button type="submit" class="account-locked-button">
        {{ __('app.auth.account.logout') }}
      </button>
    </form>
  </main>
</body>
</html>
