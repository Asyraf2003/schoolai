@php
  $createFailed = old('form_context') === 'create';

  $editingId = old('form_context') === 'update'
      ? (int) old('editing_id')
      : null;

  $archivedStatistics = collect($archivedStatistics ?? []);
  $replacementCandidatesByArchivedId = collect($replacementCandidatesByArchivedId ?? []);

  $createLanguageCompletion = [
    'id' => filled($createFailed ? old('value') : null) && filled($createFailed ? old('label') : null),
    'en' => filled($createFailed ? old('value_en') : null) && filled($createFailed ? old('label_en') : null),
    'ar' => filled($createFailed ? old('value_ar') : null) && filled($createFailed ? old('label_ar') : null),
  ];

  $createActiveLanguage = $createFailed && $errors->hasAny(['value_ar', 'label_ar'])
      ? 'ar'
      : ($createFailed && $errors->hasAny(['value_en', 'label_en']) ? 'en' : 'id');
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
        Indonesia adalah bahasa utama. English dan Arabic opsional dan akan memakai fallback jika belum diisi.
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
          Isi Indonesia sebagai data utama. English dan Arabic dapat ditambahkan sekarang atau nanti.
          Arsip tidak dihitung ke batas aktif.
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

          <div data-language-tabs>
            <div class="admin-inline-actions" role="tablist" aria-label="Bahasa statistik baru">
              <button type="button" class="admin-primary-action {{ $createActiveLanguage === 'id' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $createActiveLanguage === 'id' ? 'true' : 'false' }}" data-language-tab="id">
                Indonesia · Utama {{ $createLanguageCompletion['id'] ? '✓' : 'Belum' }}
              </button>
              <button type="button" class="admin-primary-action {{ $createActiveLanguage === 'en' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $createActiveLanguage === 'en' ? 'true' : 'false' }}" data-language-tab="en">
                English {{ $createLanguageCompletion['en'] ? '✓' : 'Belum' }}
              </button>
              <button type="button" class="admin-primary-action {{ $createActiveLanguage === 'ar' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $createActiveLanguage === 'ar' ? 'true' : 'false' }}" data-language-tab="ar">
                العربية {{ $createLanguageCompletion['ar'] ? '✓' : 'Belum' }}
              </button>
            </div>

            <section class="stats-manager-language" data-language-panel="id" @if($createActiveLanguage !== 'id') hidden @endif>
              <div class="stats-manager-fields">
                <div class="admin-field">
                  <label for="stats-create-value">Nilai Indonesia</label>
                  <input
                    id="stats-create-value"
                    name="value"
                    maxlength="80"
                    value="{{ $createFailed ? old('value') : '' }}"
                    placeholder="Contoh: 250+"
                    required
                  >
                  @error('value')<small>{{ $message }}</small>@enderror
                </div>

                <div class="admin-field">
                  <label for="stats-create-label">Label Indonesia</label>
                  <input
                    id="stats-create-label"
                    name="label"
                    maxlength="120"
                    value="{{ $createFailed ? old('label') : '' }}"
                    placeholder="Contoh: Siswa aktif"
                    required
                  >
                  @error('label')<small>{{ $message }}</small>@enderror
                </div>
              </div>
            </section>

            <section class="stats-manager-language" data-language-panel="en" @if($createActiveLanguage !== 'en') hidden @endif>
              <div class="stats-manager-fields">
                <div class="admin-field">
                  <label for="stats-create-value-en">Nilai English</label>
                  <input
                    id="stats-create-value-en"
                    name="value_en"
                    maxlength="80"
                    value="{{ $createFailed ? old('value_en') : '' }}"
                    placeholder="Example: 250+"
                    lang="en"
                  >
                  @error('value_en')<small>{{ $message }}</small>@enderror
                </div>

                <div class="admin-field">
                  <label for="stats-create-label-en">Label English</label>
                  <input
                    id="stats-create-label-en"
                    name="label_en"
                    maxlength="120"
                    value="{{ $createFailed ? old('label_en') : '' }}"
                    placeholder="Example: Active students"
                    lang="en"
                  >
                  <em>Opsional. Jika kosong, publik English fallback ke Indonesia.</em>
                  @error('label_en')<small>{{ $message }}</small>@enderror
                </div>
              </div>
            </section>

            <section class="stats-manager-language" data-language-panel="ar" @if($createActiveLanguage !== 'ar') hidden @endif>
              <div class="stats-manager-fields">
                <div class="admin-field">
                  <label for="stats-create-value-ar">Nilai Arabic</label>
                  <input
                    id="stats-create-value-ar"
                    name="value_ar"
                    maxlength="80"
                    value="{{ $createFailed ? old('value_ar') : '' }}"
                    placeholder="مثال: +250"
                    lang="ar"
                    dir="rtl"
                  >
                  @error('value_ar')<small>{{ $message }}</small>@enderror
                </div>

                <div class="admin-field">
                  <label for="stats-create-label-ar">Label Arabic</label>
                  <input
                    id="stats-create-label-ar"
                    name="label_ar"
                    maxlength="120"
                    value="{{ $createFailed ? old('label_ar') : '' }}"
                    placeholder="مثال: الطلاب النشطون"
                    lang="ar"
                    dir="rtl"
                  >
                  <em>Opsional. Jika kosong, publik Arabic fallback ke Indonesia lalu English.</em>
                  @error('label_ar')<small>{{ $message }}</small>@enderror
                </div>
              </div>
            </section>
          </div>

          <div class="stats-manager-form-actions">
            <button type="submit" class="admin-primary-action">Tambah Statistik</button>
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

          $editValues = [
            'value' => $isCurrentEdit ? old('value') : $statistic->value,
            'label' => $isCurrentEdit ? old('label') : $statistic->label,
            'value_en' => $isCurrentEdit ? old('value_en') : $statistic->value_en,
            'label_en' => $isCurrentEdit ? old('label_en') : $statistic->label_en,
            'value_ar' => $isCurrentEdit ? old('value_ar') : $statistic->value_ar,
            'label_ar' => $isCurrentEdit ? old('label_ar') : $statistic->label_ar,
          ];

          $editLanguageCompletion = [
            'id' => filled($editValues['value']) && filled($editValues['label']),
            'en' => filled($editValues['value_en']) && filled($editValues['label_en']),
            'ar' => filled($editValues['value_ar']) && filled($editValues['label_ar']),
          ];

          $editActiveLanguage = $isCurrentEdit && $errors->hasAny(['value_ar', 'label_ar'])
              ? 'ar'
              : ($isCurrentEdit && $errors->hasAny(['value_en', 'label_en']) ? 'en' : 'id');
        @endphp

        <article class="stats-manager-card">
          <header class="stats-manager-card__head">
            <div>
              <span class="stats-manager-kicker">Posisi {{ $index + 1 }}</span>

              <strong>{{ $statistic->value }} · {{ $statistic->label }}</strong>

              <small>
                EN: {{ filled($statistic->value_en) && filled($statistic->label_en) ? $statistic->value_en . ' · ' . $statistic->label_en : 'belum diterjemahkan' }}
                · AR: {{ filled($statistic->value_ar) && filled($statistic->label_ar) ? $statistic->value_ar . ' · ' . $statistic->label_ar : 'belum diterjemahkan' }}
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
            <input type="hidden" name="editing_id" value="{{ $statistic->id }}">

            <div data-language-tabs>
              <div class="admin-inline-actions" role="tablist" aria-label="Bahasa statistik posisi {{ $index + 1 }}">
                <button type="button" class="admin-primary-action {{ $editActiveLanguage === 'id' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $editActiveLanguage === 'id' ? 'true' : 'false' }}" data-language-tab="id">
                  Indonesia · Utama {{ $editLanguageCompletion['id'] ? '✓' : 'Belum' }}
                </button>
                <button type="button" class="admin-primary-action {{ $editActiveLanguage === 'en' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $editActiveLanguage === 'en' ? 'true' : 'false' }}" data-language-tab="en">
                  English {{ $editLanguageCompletion['en'] ? '✓' : 'Belum' }}
                </button>
                <button type="button" class="admin-primary-action {{ $editActiveLanguage === 'ar' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $editActiveLanguage === 'ar' ? 'true' : 'false' }}" data-language-tab="ar">
                  العربية {{ $editLanguageCompletion['ar'] ? '✓' : 'Belum' }}
                </button>
              </div>

              <section class="stats-manager-language" data-language-panel="id" @if($editActiveLanguage !== 'id') hidden @endif>
                <div class="stats-manager-fields">
                  <div class="admin-field">
                    <label for="stat-value-{{ $statistic->id }}">Nilai Indonesia</label>
                    <input id="stat-value-{{ $statistic->id }}" name="value" maxlength="80" value="{{ $editValues['value'] }}" required>
                    @if($isCurrentEdit) @error('value')<small>{{ $message }}</small>@enderror @endif
                  </div>

                  <div class="admin-field">
                    <label for="stat-label-{{ $statistic->id }}">Label Indonesia</label>
                    <input id="stat-label-{{ $statistic->id }}" name="label" maxlength="120" value="{{ $editValues['label'] }}" required>
                    @if($isCurrentEdit) @error('label')<small>{{ $message }}</small>@enderror @endif
                  </div>
                </div>
              </section>

              <section class="stats-manager-language" data-language-panel="en" @if($editActiveLanguage !== 'en') hidden @endif>
                <div class="stats-manager-fields">
                  <div class="admin-field">
                    <label for="stat-value-en-{{ $statistic->id }}">Nilai English</label>
                    <input id="stat-value-en-{{ $statistic->id }}" name="value_en" maxlength="80" value="{{ $editValues['value_en'] }}" lang="en">
                    @if($isCurrentEdit) @error('value_en')<small>{{ $message }}</small>@enderror @endif
                  </div>

                  <div class="admin-field">
                    <label for="stat-label-en-{{ $statistic->id }}">Label English</label>
                    <input id="stat-label-en-{{ $statistic->id }}" name="label_en" maxlength="120" value="{{ $editValues['label_en'] }}" lang="en">
                    <em>Opsional. Jika kosong, publik English fallback ke Indonesia.</em>
                    @if($isCurrentEdit) @error('label_en')<small>{{ $message }}</small>@enderror @endif
                  </div>
                </div>
              </section>

              <section class="stats-manager-language" data-language-panel="ar" @if($editActiveLanguage !== 'ar') hidden @endif>
                <div class="stats-manager-fields">
                  <div class="admin-field">
                    <label for="stat-value-ar-{{ $statistic->id }}">Nilai Arabic</label>
                    <input id="stat-value-ar-{{ $statistic->id }}" name="value_ar" maxlength="80" value="{{ $editValues['value_ar'] }}" lang="ar" dir="rtl">
                    @if($isCurrentEdit) @error('value_ar')<small>{{ $message }}</small>@enderror @endif
                  </div>

                  <div class="admin-field">
                    <label for="stat-label-ar-{{ $statistic->id }}">Label Arabic</label>
                    <input id="stat-label-ar-{{ $statistic->id }}" name="label_ar" maxlength="120" value="{{ $editValues['label_ar'] }}" lang="ar" dir="rtl">
                    <em>Opsional. Jika kosong, publik Arabic fallback ke Indonesia lalu English.</em>
                    @if($isCurrentEdit) @error('label_ar')<small>{{ $message }}</small>@enderror @endif
                  </div>
                </div>
              </section>
            </div>

            <div class="stats-manager-form-actions">
              <button type="submit" class="admin-primary-action">Simpan Perubahan</button>
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
                  EN: {{ filled($statistic->value_en) && filled($statistic->label_en) ? $statistic->value_en . ' · ' . $statistic->label_en : 'belum diterjemahkan' }}
                  · AR: {{ filled($statistic->value_ar) && filled($statistic->label_ar) ? $statistic->value_ar . ' · ' . $statistic->label_ar : 'belum diterjemahkan' }}
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
                <span class="admin-archive-note">Empat slot aktif penuh dan tidak ada statistik berlabel Indonesia identik.</span>
              @endif
            </div>
          </article>
        @endforeach
      @endif
    </section>
  </div>

  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      document.querySelectorAll('[data-language-tabs]').forEach((tabsRoot) => {
        const tabs = [...tabsRoot.querySelectorAll('[data-language-tab]')];
        const panels = [...tabsRoot.querySelectorAll('[data-language-panel]')];

        function activateLanguage(locale) {
          tabs.forEach((tab) => {
            const isActive = tab.dataset.languageTab === locale;
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            tab.classList.toggle('admin-primary-action--ghost', !isActive);
          });

          panels.forEach((panel) => {
            panel.hidden = panel.dataset.languagePanel !== locale;
          });
        }

        tabs.forEach((tab) => {
          tab.addEventListener('click', () => activateLanguage(tab.dataset.languageTab));
        });
      });
    })();
  </script>
@endsection
