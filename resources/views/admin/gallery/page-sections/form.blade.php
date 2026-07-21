@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.sections.update', $section) : route('admin.galeri.sections.store');

  $languageCompletion = [
    'id' => filled(old('title_id', $section->title_id)),
    'en' => filled(old('title_en', $section->title_en)),
    'ar' => filled(old('title_ar', $section->title_ar)),
  ];

  $activeLanguage = $errors->hasAny(['title_ar', 'description_ar'])
    ? 'ar'
    : ($errors->hasAny(['title_en', 'description_en']) ? 'en' : 'id');
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Bagian Galeri' : 'Tambah Bagian Galeri',
  'activeAdminPage' => 'galeri',
])

@section('content')
  <form method="POST" action="{{ $action }}" class="gallery-lite-form" data-gallery-section-form>
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h1>{{ $isEdit ? 'Edit Bagian Galeri' : 'Tambah Bagian Galeri' }}</h1>
        <p>Indonesia adalah bahasa utama. English dan Arabic opsional; jika kosong, halaman galeri publik akan memakai fallback yang tersedia.</p>
      </div>

      <div class="admin-inline-actions">
        <a href="{{ $isEdit ? route('admin.galeri.sections.show', $section) : route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
        <button type="submit" class="admin-primary-action">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
      </div>
    </header>

    @if(isset($errors) && $errors->any())
      <div class="admin-error-box" role="alert">
        @foreach($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <section class="gallery-lite-form__panel" data-language-tabs>
      <div class="admin-inline-actions" role="tablist" aria-label="Bahasa bagian galeri">
        <button
          type="button"
          class="admin-primary-action {{ $activeLanguage === 'id' ? '' : 'admin-primary-action--ghost' }}"
          role="tab"
          aria-selected="{{ $activeLanguage === 'id' ? 'true' : 'false' }}"
          data-language-tab="id"
        >
          Indonesia · Utama {{ $languageCompletion['id'] ? '✓' : 'Belum' }}
        </button>
        <button
          type="button"
          class="admin-primary-action {{ $activeLanguage === 'en' ? '' : 'admin-primary-action--ghost' }}"
          role="tab"
          aria-selected="{{ $activeLanguage === 'en' ? 'true' : 'false' }}"
          data-language-tab="en"
        >
          English {{ $languageCompletion['en'] ? '✓' : 'Belum' }}
        </button>
        <button
          type="button"
          class="admin-primary-action {{ $activeLanguage === 'ar' ? '' : 'admin-primary-action--ghost' }}"
          role="tab"
          aria-selected="{{ $activeLanguage === 'ar' ? 'true' : 'false' }}"
          data-language-tab="ar"
        >
          العربية {{ $languageCompletion['ar'] ? '✓' : 'Belum' }}
        </button>
      </div>

      <div class="gallery-lite-form__grid" data-language-panel="id" @if($activeLanguage !== 'id') hidden @endif>
        <div class="admin-field admin-field--wide">
          <label for="title_id">Judul Indonesia</label>
          <input id="title_id" name="title_id" value="{{ old('title_id', $section->title_id) }}" required>
          @error('title_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="description_id">Deskripsi Indonesia</label>
          <textarea id="description_id" name="description_id" rows="5">{{ old('description_id', $section->description_id) }}</textarea>
          <em>Opsional.</em>
          @error('description_id') <small>{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="gallery-lite-form__grid" data-language-panel="en" @if($activeLanguage !== 'en') hidden @endif>
        <div class="admin-field admin-field--wide">
          <label for="title_en">Judul English</label>
          <input id="title_en" name="title_en" value="{{ old('title_en', $section->title_en) }}" lang="en">
          @error('title_en') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="description_en">Deskripsi English</label>
          <textarea id="description_en" name="description_en" rows="5" lang="en">{{ old('description_en', $section->description_en) }}</textarea>
          <em>Opsional. Jika kosong, versi English fallback ke Indonesia.</em>
          @error('description_en') <small>{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="gallery-lite-form__grid" data-language-panel="ar" @if($activeLanguage !== 'ar') hidden @endif>
        <div class="admin-field admin-field--wide">
          <label for="title_ar">Judul Arabic</label>
          <input id="title_ar" name="title_ar" value="{{ old('title_ar', $section->title_ar) }}" lang="ar" dir="rtl">
          @error('title_ar') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="description_ar">Deskripsi Arabic</label>
          <textarea id="description_ar" name="description_ar" rows="5" lang="ar" dir="rtl">{{ old('description_ar', $section->description_ar) }}</textarea>
          <em>Opsional. Jika kosong, versi Arabic fallback ke Indonesia lalu English.</em>
          @error('description_ar') <small>{{ $message }}</small> @enderror
        </div>
      </div>
    </section>

    <section class="gallery-lite-form__panel">
      <div class="gallery-lite-form__grid">
        <label class="admin-check-field">
          <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $section->is_published))>
          <span>Aktif</span>
        </label>
      </div>
    </section>
  </form>

  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      const form = document.querySelector('[data-gallery-section-form]');
      if (!form) return;

      const tabsRoot = form.querySelector('[data-language-tabs]');
      if (!tabsRoot) return;

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
    })();
  </script>
@endsection
