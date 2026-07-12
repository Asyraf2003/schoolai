@php
  $createFailed = old('form_context') === 'create';
  $editingId = old('form_context') === 'update'
      ? (int) old('editing_id')
      : null;
@endphp

@extends('layouts.admin', [
  'title' => 'Admin Statistik Homepage',
  'activeAdminPage' => 'stats',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Statistik Homepage</h1>
      <p>
        Kelola angka dan label pada pita statistik homepage.
        Maksimal {{ $maxItems }} item agar tampilan publik tetap rapi.
      </p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">
        {{ $statistics->count() }}/{{ $maxItems }} statistik
      </span>

      <a
        href="{{ route('home') }}"
        target="_blank"
        rel="noopener"
        class="admin-primary-action admin-primary-action--ghost"
      >
        Lihat Homepage
      </a>
    </div>
  </header>

  @if(session('success'))
    <p class="flash-message" role="status">{{ session('success') }}</p>
  @endif

  @if($errors->any())
    <div class="admin-error-box" role="alert">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <div class="stats-manager-shell">
    <section
      class="admin-content-panel stats-manager-create"
      aria-labelledby="stats-create-title"
    >
      <div class="stats-manager-section-head">
        <span class="stats-manager-kicker">Tambah data</span>
        <h2 id="stats-create-title">Statistik baru</h2>
        <p>Contoh nilai: 250+, A, 100%, atau 12 Tahun.</p>
      </div>

      @if($canCreate)
        <form
          method="POST"
          action="{{ route('admin.stats.store') }}"
          class="stats-manager-form"
        >
          @csrf
          <input type="hidden" name="form_context" value="create">

          <div class="stats-manager-fields">
            <div class="admin-field">
              <label for="stats-create-value">Nilai</label>
              <input
                id="stats-create-value"
                name="value"
                maxlength="80"
                value="{{ $createFailed ? old('value') : '' }}"
                placeholder="Contoh: 250+"
                required
              >
            </div>

            <div class="admin-field">
              <label for="stats-create-label">Label</label>
              <input
                id="stats-create-label"
                name="label"
                maxlength="120"
                value="{{ $createFailed ? old('label') : '' }}"
                placeholder="Contoh: Siswa aktif"
                required
              >
            </div>

            <button type="submit" class="admin-primary-action">
              Tambah Statistik
            </button>
          </div>
        </form>
      @else
        <p class="admin-notice">
          Batas {{ $maxItems }} statistik sudah tercapai.
          Hapus salah satu item sebelum menambahkan yang baru.
        </p>
      @endif
    </section>

    <section
      class="stats-manager-list"
      aria-label="Daftar statistik homepage"
    >
      @foreach($statistics as $index => $statistic)
        @php
          $isCurrentEdit = $editingId === $statistic->id;
        @endphp

        <article class="stats-manager-card">
          <header class="stats-manager-card__head">
            <div>
              <span class="stats-manager-kicker">
                Posisi {{ $index + 1 }}
              </span>
              <strong>{{ $statistic->value }}</strong>
              <small>{{ $statistic->label }}</small>
            </div>

            <span class="gallery-lite-status is-active">Aktif</span>
          </header>

          <form
            method="POST"
            action="{{ route('admin.stats.update', $statistic) }}"
            class="stats-manager-form"
          >
            @csrf
            @method('PUT')

            <input type="hidden" name="form_context" value="update">
            <input
              type="hidden"
              name="editing_id"
              value="{{ $statistic->id }}"
            >

            <div class="stats-manager-fields">
              <div class="admin-field">
                <label for="stat-value-{{ $statistic->id }}">Nilai</label>
                <input
                  id="stat-value-{{ $statistic->id }}"
                  name="value"
                  maxlength="80"
                  value="{{ $isCurrentEdit ? old('value') : $statistic->value }}"
                  required
                >
              </div>

              <div class="admin-field">
                <label for="stat-label-{{ $statistic->id }}">Label</label>
                <input
                  id="stat-label-{{ $statistic->id }}"
                  name="label"
                  maxlength="120"
                  value="{{ $isCurrentEdit ? old('label') : $statistic->label }}"
                  required
                >
              </div>

              <button type="submit" class="admin-primary-action">
                Simpan
              </button>
            </div>
          </form>

          <div class="stats-manager-card__actions">
            <form
              method="POST"
              action="{{ route('admin.stats.move-up', $statistic) }}"
            >
              @csrf
              @method('PATCH')
              <button
                type="submit"
                class="admin-small-action admin-small-action--ghost"
                @disabled($loop->first)
              >
                Naik
              </button>
            </form>

            <form
              method="POST"
              action="{{ route('admin.stats.move-down', $statistic) }}"
            >
              @csrf
              @method('PATCH')
              <button
                type="submit"
                class="admin-small-action admin-small-action--ghost"
                @disabled($loop->last)
              >
                Turun
              </button>
            </form>

            <form
              method="POST"
              action="{{ route('admin.stats.destroy', $statistic) }}"
              data-admin-delete-form
              data-admin-delete-message="Hapus statistik {{ $statistic->value }} · {{ $statistic->label }}?"
            >
              @csrf
              @method('DELETE')

              <button
                type="button"
                class="admin-small-action admin-small-action--danger"
                data-admin-delete-trigger
                @disabled($statistics->count() <= 1)
              >
                Hapus
              </button>
            </form>
          </div>
        </article>
      @endforeach
    </section>
  </div>
@endsection
