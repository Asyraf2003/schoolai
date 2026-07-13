@php
  $createFailed = old('form_context') === 'create';

  $editingId = old('form_context') === 'update'
      ? (int) old('editing_id')
      : null;

  $archivedStatistics = collect($archivedStatistics ?? []);
  $replacementCandidatesByArchivedId = collect($replacementCandidatesByArchivedId ?? []);
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
        Kelola nilai dan label dalam bahasa Indonesia dan English.
        Maksimal {{ $maxItems }} item aktif agar tampilan homepage tetap rapi.
      </p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">
        {{ $statistics->count() }}/{{ $maxItems }} aktif · {{ $archivedStatistics->count() }} arsip
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
        <p>
          Isi kedua bahasa agar halaman English tidak menampilkan
          label Indonesia secara tidak sengaja. Arsip tidak dihitung ke batas aktif.
        </p>
      </div>

      @if($canCreate)
        <form
          method="POST"
          action="{{ route('admin.stats.store') }}"
          class="stats-manager-form"
        >
          @csrf
          <input type="hidden" name="form_context" value="create">

          <div class="stats-manager-language-grid">
            <section class="stats-manager-language">
              <h3>Indonesia</h3>

              <div class="stats-manager-fields">
                <div class="admin-field">
                  <label for="stats-create-value">
                    Nilai Indonesia
                  </label>
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
                  <label for="stats-create-label">
                    Label Indonesia
                  </label>
                  <input
                    id="stats-create-label"
                    name="label"
                    maxlength="120"
                    value="{{ $createFailed ? old('label') : '' }}"
                    placeholder="Contoh: Siswa aktif"
                    required
                  >
                </div>
              </div>
            </section>

            <section class="stats-manager-language">
              <h3>English</h3>

              <div class="stats-manager-fields">
                <div class="admin-field">
                  <label for="stats-create-value-en">
                    Nilai English
                  </label>
                  <input
                    id="stats-create-value-en"
                    name="value_en"
                    maxlength="80"
                    value="{{ $createFailed ? old('value_en') : '' }}"
                    placeholder="Example: 250+"
                    required
                  >
                </div>

                <div class="admin-field">
                  <label for="stats-create-label-en">
                    Label English
                  </label>
                  <input
                    id="stats-create-label-en"
                    name="label_en"
                    maxlength="120"
                    value="{{ $createFailed ? old('label_en') : '' }}"
                    placeholder="Example: Active students"
                    required
                  >
                </div>
              </div>
            </section>
          </div>

          <div class="stats-manager-form-actions">
            <button type="submit" class="admin-primary-action">
              Tambah Statistik
            </button>
          </div>
        </form>
      @else
        <p class="admin-notice">
          Batas {{ $maxItems }} statistik aktif sudah tercapai.
          Arsipkan salah satu item aktif atau gunakan Pulihkan &amp; Gantikan pada arsip yang identik.
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

              <strong>
                {{ $statistic->value }}
                ·
                {{ $statistic->label }}
              </strong>

              <small>
                EN:
                {{ $statistic->valueForLocale('en') }}
                ·
                {{ $statistic->labelForLocale('en') }}
              </small>
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

            <div class="stats-manager-language-grid">
              <section class="stats-manager-language">
                <h3>Indonesia</h3>

                <div class="stats-manager-fields">
                  <div class="admin-field">
                    <label for="stat-value-{{ $statistic->id }}">
                      Nilai Indonesia
                    </label>
                    <input
                      id="stat-value-{{ $statistic->id }}"
                      name="value"
                      maxlength="80"
                      value="{{ $isCurrentEdit ? old('value') : $statistic->value }}"
                      required
                    >
                  </div>

                  <div class="admin-field">
                    <label for="stat-label-{{ $statistic->id }}">
                      Label Indonesia
                    </label>
                    <input
                      id="stat-label-{{ $statistic->id }}"
                      name="label"
                      maxlength="120"
                      value="{{ $isCurrentEdit ? old('label') : $statistic->label }}"
                      required
                    >
                  </div>
                </div>
              </section>

              <section class="stats-manager-language">
                <h3>English</h3>

                <div class="stats-manager-fields">
                  <div class="admin-field">
                    <label for="stat-value-en-{{ $statistic->id }}">
                      Nilai English
                    </label>
                    <input
                      id="stat-value-en-{{ $statistic->id }}"
                      name="value_en"
                      maxlength="80"
                      value="{{ $isCurrentEdit ? old('value_en') : $statistic->valueForLocale('en') }}"
                      required
                    >
                  </div>

                  <div class="admin-field">
                    <label for="stat-label-en-{{ $statistic->id }}">
                      Label English
                    </label>
                    <input
                      id="stat-label-en-{{ $statistic->id }}"
                      name="label_en"
                      maxlength="120"
                      value="{{ $isCurrentEdit ? old('label_en') : $statistic->labelForLocale('en') }}"
                      required
                    >
                  </div>
                </div>
              </section>
            </div>

            <div class="stats-manager-form-actions">
              <button type="submit" class="admin-primary-action">
                Simpan Perubahan
              </button>
            </div>
          </form>

          <div class="stats-manager-card__actions">
            <form
              method="POST"
              action="{{ route('admin.stats.destroy', $statistic) }}"
              data-admin-delete-form
              data-admin-delete-message="Hapus statistik {{ $statistic->value }} · {{ $statistic->label }}? Data tetap disimpan sebagai arsip."
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

      @if($archivedStatistics->isNotEmpty())
        <div class="stats-manager-archive-head">
          <span class="stats-manager-kicker">Arsip</span>
          <h2>Statistik yang dihapus</h2>
          <p>Arsip tidak muncul di homepage dan tidak dapat diedit sebelum dipulihkan.</p>
        </div>

        @foreach($archivedStatistics as $index => $statistic)
          @php($replacementCandidates = $replacementCandidatesByArchivedId->get($statistic->getKey(), collect()))

          <article class="stats-manager-card is-deleted">
            <header class="stats-manager-card__head">
              <div>
                <span class="stats-manager-kicker">Arsip A{{ $index + 1 }}</span>
                <strong>{{ $statistic->value }} · {{ $statistic->label }}</strong>
                <small>
                  EN: {{ $statistic->valueForLocale('en') }} · {{ $statistic->labelForLocale('en') }}
                  · dihapus {{ optional($statistic->deleted_at)->translatedFormat('d M Y, H:i') }} WIB
                </small>
              </div>

              <span class="gallery-lite-status is-deleted">Dihapus</span>
            </header>

            <div class="stats-manager-card__actions">
              @if($canRestoreWithoutReplacement)
                <form method="POST" action="{{ route('admin.stats.restore', $statistic->getKey()) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
                </form>
              @endif

              @foreach($replacementCandidates as $candidate)
                <form method="POST" action="{{ route('admin.stats.restore', $statistic->getKey()) }}" data-admin-delete-form data-admin-delete-message="Pulihkan statistik arsip ini dan pindahkan statistik aktif #{{ $candidate->getKey() }} ke arsip?">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="replacement_site_statistic_id" value="{{ $candidate->getKey() }}">
                  <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--restore-swap">Pulihkan &amp; Gantikan #{{ $candidate->getKey() }}</button>
                </form>
              @endforeach

              @if(! $canRestoreWithoutReplacement && $replacementCandidates->isEmpty())
                <span class="admin-archive-note">Empat slot aktif penuh dan tidak ada statistik berlabel identik.</span>
              @endif
            </div>
          </article>
        @endforeach
      @endif
    </section>
  </div>
@endsection
