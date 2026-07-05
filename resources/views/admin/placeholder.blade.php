{{-- ADMIN_DESKTOP_DUMMY_PLACEHOLDER_FINAL --}}
@php
  $adminPageKey = $adminPageKey ?? 'dashboard';
  $adminPage = __('admin.pages.' . $adminPageKey);

  if (! is_array($adminPage)) {
      $adminPageKey = 'dashboard';
      $adminPage = __('admin.pages.dashboard');
  }

  $adminVisuals = [
      'dashboard' => '▦',
      'ppdb' => '✎',
      'artikel' => '¶',
      'galeri' => '◇',
  ];
@endphp

@extends('layouts.admin', [
  'title' => $adminPage['title'],
  'activeAdminPage' => $adminPageKey,
])

@section('content')
  <header class="admin-topbar">
    <div>
      <p class="admin-topbar__eyebrow">{{ __('admin.shell.eyebrow') }}</p>
      <h1>{{ $adminPage['heading'] }}</h1>
      <p>{{ $adminPage['description'] }}</p>
    </div>

    <span class="admin-status-pill">{{ __('admin.shell.status') }}</span>
  </header>

  <p class="admin-notice">{{ __('admin.shell.notice') }}</p>

  <section class="admin-content-panel" aria-labelledby="admin-empty-title">
    <div class="admin-empty-hero">
      <div>
        <h2 id="admin-empty-title">{{ $adminPage['empty_title'] }}</h2>
        <p>{{ $adminPage['empty_description'] }}</p>
      </div>

      <div class="admin-empty-visual" aria-hidden="true">
        {{ $adminVisuals[$adminPageKey] ?? '▦' }}
      </div>
    </div>

    <div class="admin-card-grid">
      @foreach ($adminPage['cards'] as $card)
        <article class="admin-dummy-card">
          <span>{{ $card['label'] }}</span>
          <strong>{{ $card['value'] }}</strong>
        </article>
      @endforeach
    </div>
  </section>
@endsection
