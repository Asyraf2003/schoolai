@php
  $adminPageKey = $adminPageKey ?? 'dashboard';
  $adminPage = __('admin.pages.' . $adminPageKey);

  if (! is_array($adminPage)) {
      $adminPageKey = 'dashboard';
      $adminPage = __('admin.pages.dashboard');
  }

  $simpleText = $adminPageKey === 'dashboard'
      ? 'ini dashboard'
      : 'ini ' . strtolower((string) $adminPageKey);
@endphp

@extends('layouts.admin', [
  'title' => $adminPage['title'],
  'activeAdminPage' => $adminPageKey,
])

@section('content')
  <section class="admin-simple-page" aria-labelledby="admin-simple-title">
    <h1 id="admin-simple-title">{{ $adminPage['heading'] }}</h1>
    <p>{{ $simpleText }}</p>
  </section>
@endsection
