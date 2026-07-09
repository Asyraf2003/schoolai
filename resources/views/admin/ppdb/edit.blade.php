{{-- ADMIN_PPDB_SETTING_FINAL --}}
@extends('layouts.admin', [
  'title' => 'Admin PPDB',
  'activeAdminPage' => 'ppdb',
])

@php
  $showcaseItems = collect($showcaseItems ?? []);
  $showcaseItemsByAudience = $showcaseItems->groupBy('audience');
  $showcaseFormMode = $showcaseFormMode ?? 'create';
  $showcaseItemForm = $showcaseItemForm ?? null;
  $showcaseFormIsEdit = $showcaseFormMode === 'edit' && $showcaseItemForm?->exists;
  $showcaseAudience = old('audience', $showcaseItemForm->audience ?? 'parents');
  $showcaseMediaType = old('media_type', $showcaseItemForm->media_type ?? 'photo');
@endphp

@section('content')
  <style>
    .ppdb-showcase-admin { margin-top: 22px; display: grid; gap: 18px; }
    .ppdb-showcase-admin__grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(360px, 0.68fr); gap: 18px; align-items: start; }
    .ppdb-showcase-admin__panel { padding: 18px; }
    .ppdb-showcase-admin__head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; margin-bottom: 14px; }
    .ppdb-showcase-admin__head h2, .ppdb-showcase-admin__head p { margin: 0; }
    .ppdb-showcase-admin__head p { margin-top: 6px; color: var(--admin-muted); line-height: 1.55; }
    .ppdb-showcase-list { display: grid; gap: 14px; }
    .ppdb-showcase-group { border: 1px solid var(--admin-line); border-radius: 18px; overflow: hidden; background: #fff; }
    .ppdb-showcase-group__title { display: flex; justify-content: space-between; gap: 12px; padding: 12px 14px; background: #fffdf8; border-bottom: 1px solid var(--admin-line); font-weight: 950; }
    .ppdb-showcase-row { display: grid; grid-template-columns: 42px minmax(0, 1fr) 104px minmax(260px, auto); align-items: center; gap: 12px; padding: 13px 14px; border-bottom: 1px solid var(--admin-line); }
    .ppdb-showcase-row:last-child { border-bottom: 0; }
    .ppdb-showcase-row__order { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 999px; background: #f6f3ec; color: var(--admin-ink); font-weight: 950; }
    .ppdb-showcase-row__body { min-width: 0; display: grid; gap: 3px; }
    .ppdb-showcase-row__body strong, .ppdb-showcase-row__body small { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
    .ppdb-showcase-row__body small { color: var(--admin-muted); }
    .ppdb-showcase-media-pill { justify-self: end; padding: 6px 10px; border-radius: 999px; background: #eef2ff; color: #3730a3; font-size: 0.78rem; font-weight: 950; }
    .ppdb-showcase-form-note { margin: 0 0 14px; padding: 12px 14px; border-radius: 16px; background: #fff7ed; color: #9a3412; font-weight: 780; line-height: 1.55; }
    .ppdb-showcase-preview { margin-top: 14px; }
    .ppdb-showcase-preview__stage { min-height: 190px; }
    .ppdb-showcase-preview__stage img, .ppdb-showcase-preview__stage iframe { width: 100%; min-height: 190px; display: block; border: 0; object-fit: cover; background: #111827; }
    .ppdb-media-input[hidden] { display: none; }
    @media (max-width: 1180px) { .ppdb-showcase-admin__grid { grid-template-columns: 1fr; } }
  </style>

  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Pengaturan PPDB</h1>
      <p>Kelola link pendaftaran, status buka/tutup PPDB, dan konten publik halaman PPDB.</p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $setting->is_active ? 'PPDB aktif' : 'PPDB nonaktif' }}</span>
      <a href="{{ route('ppdb') }}" class="admin-primary-action admin-primary-action--ghost" target="_blank" rel="noopener noreferrer">Lihat Halaman PPDB</a>
    </div>
  </header>

  @if (session('success'))
    <p class="flash-message">{{ session('success') }}</p>
  @endif

  @if ($errors->any())
    <div class="admin-error-box">
      @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <section class="admin-content-panel">
    <form method="POST" action="{{ route('admin.ppdb.update') }}" class="gallery-lite-form">
      @csrf
      @method('PUT')

      <div class="gallery-lite-form__panel">
        <div class="gallery-lite-form__grid">
          <div class="admin-field admin-field--wide">
            <label for="registration_url">Link formulir pendaftaran PPDB</label>
            <input id="registration_url" type="url" name="registration_url" value="{{ old('registration_url', $setting->registration_url) }}" maxlength="2048" placeholder="https://forms.gle/..." required>
            <em>Dipakai oleh tombol daftar di halaman publik.</em>
            @error('registration_url')<small>{{ $message }}</small>@enderror
          </div>

          <div class="admin-field admin-field--wide">
            <label for="information_url">Link info / brosur / PDF PPDB</label>
            <input id="information_url" type="url" name="information_url" value="{{ old('information_url', $setting->information_url) }}" maxlength="2048" placeholder="https://example.com/brosur-ppdb.pdf">
            <em>Opsional. Dipakai oleh tombol panduan PPDB.</em>
            @error('information_url')<small>{{ $message }}</small>@enderror
          </div>

          <div class="admin-field admin-field--wide">
            <input type="hidden" name="is_active" value="0">
            <label class="admin-check-field" for="is_active">
              <input id="is_active" type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $setting->is_active))>
              <span>Aktifkan pendaftaran publik</span>
            </label>
            <em>Jika dimatikan, tombol daftar menampilkan modal pendaftaran ditutup.</em>
          </div>
        </div>
      </div>

      <div class="admin-inline-actions">
        <button type="submit" class="admin-primary-action">Simpan Pengaturan</button>
      </div>
    </form>
  </section>

  <section class="admin-gallery-block" style="margin-top: 18px;">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Status cepat</h2>
        <p>Buka atau tutup pendaftaran publik tanpa mengubah link.</p>
      </div>

      <form method="POST" action="{{ route('admin.ppdb.toggle') }}">
        @csrf
        @method('PATCH')
        <button type="submit" class="admin-primary-action {{ $setting->is_active ? 'admin-primary-action--danger' : '' }}">
          {{ $setting->is_active ? 'Nonaktifkan PPDB' : 'Aktifkan PPDB' }}
        </button>
      </form>
    </div>
  </section>

  <section id="ppdb-showcase-admin" class="ppdb-showcase-admin">
    <div class="admin-topbar admin-topbar--compact" style="margin-bottom: 0;">
      <div>
        <h1>Konten PPDB</h1>
        <p>Kelola item tab Orang Tua dan Sekolah.</p>
      </div>
      <span class="admin-counter">{{ $showcaseItems->count() }} item</span>
    </div>

    <div class="ppdb-showcase-admin__grid">
      <div class="gallery-lite-panel ppdb-showcase-admin__panel">
        <div class="ppdb-showcase-admin__head">
          <div>
            <h2>Daftar item</h2>
          </div>
        </div>

        <div class="ppdb-showcase-list">
          @foreach ($audienceOptions as $audience => $audienceLabel)
            @php $items = collect($showcaseItemsByAudience->get($audience, collect()))->values(); @endphp

            <div class="ppdb-showcase-group">
              <div class="ppdb-showcase-group__title">
                <span>{{ $audienceLabel }}</span>
                <small>{{ $items->count() }} item</small>
              </div>

              @forelse ($items as $item)
                <article class="ppdb-showcase-row">
                  <span class="ppdb-showcase-row__order">{{ $loop->iteration }}</span>
                  <div class="ppdb-showcase-row__body">
                    <strong>{{ $item->admin_title }}</strong>
                    <small>{{ $item->description_id }}</small>
                  </div>
                  <span class="ppdb-showcase-media-pill">{{ $item->media_type_label }}</span>

                  <div class="gallery-lite-actions">
                    <form method="POST" action="{{ route('admin.ppdb.showcase.move-up', $item) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->first)>Naik</button>
                    </form>

                    <form method="POST" action="{{ route('admin.ppdb.showcase.move-down', $item) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->last)>Turun</button>
                    </form>

                    <a href="{{ route('admin.ppdb.showcase.edit', $item) }}#ppdb-showcase-admin" class="admin-small-action">Edit</a>

                    <form method="POST" action="{{ route('admin.ppdb.showcase.destroy', $item) }}" onsubmit="return confirm('Hapus item PPDB ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="admin-small-action admin-small-action--danger">Hapus</button>
                    </form>
                  </div>
                </article>
              @empty
                <div class="gallery-lite-empty">
                  <h2>Belum ada item.</h2>
                  <p>Tambah item di form sebelah kanan.</p>
                </div>
              @endforelse
            </div>
          @endforeach
        </div>
      </div>

      <div class="gallery-lite-panel ppdb-showcase-admin__panel">
        <div class="ppdb-showcase-admin__head">
          <div>
            <h2>{{ $showcaseFormIsEdit ? 'Edit item' : 'Tambah item' }}</h2>
          </div>
          @if ($showcaseFormIsEdit)
            <a href="{{ route('admin.ppdb') }}#ppdb-showcase-admin" class="admin-small-action admin-small-action--ghost">Batal edit</a>
          @endif
        </div>

        @if (! \Illuminate\Support\Facades\Schema::hasTable('ppdb_showcase_items'))
          <p class="ppdb-showcase-form-note">Tabel item PPDB belum ada. Jalankan migration dulu.</p>
        @endif

        <form method="POST" action="{{ $showcaseFormIsEdit ? route('admin.ppdb.showcase.update', $showcaseItemForm) : route('admin.ppdb.showcase.store') }}" enctype="multipart/form-data" class="gallery-lite-form" data-ppdb-showcase-form>
          @csrf
          @if ($showcaseFormIsEdit)
            @method('PUT')
          @endif

          <div class="gallery-lite-form__grid">
            <div class="admin-field">
              <label for="showcase_audience">Target tab</label>
              <select id="showcase_audience" name="audience" required>
                @foreach ($audienceOptions as $value => $label)
                  <option value="{{ $value }}" @selected($showcaseAudience === $value)>{{ $label }}</option>
                @endforeach
              </select>
              @error('audience')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field">
              <label for="showcase_media_type">Tipe media</label>
              <select id="showcase_media_type" name="media_type" required data-ppdb-media-type>
                @foreach ($mediaTypeOptions as $value => $label)
                  <option value="{{ $value }}" @selected($showcaseMediaType === $value)>{{ $label }}</option>
                @endforeach
              </select>
              @error('media_type')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field admin-field--wide">
              <label for="showcase_title_id">Judul Indonesia</label>
              <input id="showcase_title_id" type="text" name="title_id" value="{{ old('title_id', $showcaseItemForm->title_id ?? '') }}" maxlength="180" required>
              @error('title_id')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field admin-field--wide">
              <label for="showcase_title_en">Judul Inggris</label>
              <input id="showcase_title_en" type="text" name="title_en" value="{{ old('title_en', $showcaseItemForm->title_en ?? '') }}" maxlength="180">
              <em>Boleh kosong. Jika kosong, publik EN memakai judul Indonesia.</em>
              @error('title_en')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field admin-field--wide">
              <label for="showcase_description_id">Deskripsi Indonesia</label>
              <textarea id="showcase_description_id" name="description_id" rows="4" maxlength="1200" required>{{ old('description_id', $showcaseItemForm->description_id ?? '') }}</textarea>
              @error('description_id')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field admin-field--wide">
              <label for="showcase_description_en">Deskripsi Inggris</label>
              <textarea id="showcase_description_en" name="description_en" rows="4" maxlength="1200">{{ old('description_en', $showcaseItemForm->description_en ?? '') }}</textarea>
              <em>Boleh kosong. Jika kosong, publik EN memakai deskripsi Indonesia.</em>
              @error('description_en')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field admin-field--wide ppdb-media-input" data-ppdb-media-photo>
              <label for="showcase_media_file">Upload foto</label>
              <input id="showcase_media_file" type="file" name="media_file" accept="image/jpeg,image/png,image/webp">
              <em>Wajib untuk item foto baru. Maksimal {{ $showcaseLimits['max_photo_mb'] ?? 10 }}MB.</em>
              @error('media_file')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field admin-field--wide ppdb-media-input" data-ppdb-media-url>
              <label for="showcase_media_url">URL video / embed</label>
              <input id="showcase_media_url" type="url" name="media_url" value="{{ old('media_url', $showcaseItemForm->is_video ? $showcaseItemForm->media_url : '') }}" maxlength="2048" placeholder="https://youtube.com/watch?v=...">
              <em>Wajib untuk tipe URL. Mendukung YouTube, TikTok, Instagram, atau Vimeo.</em>
              @error('media_url')<small>{{ $message }}</small>@enderror
            </div>
          </div>

          @if ($showcaseFormIsEdit && $showcaseItemForm->media_url)
            <div class="gallery-media-review ppdb-showcase-preview">
              <div class="gallery-media-review__head">
                <strong>Media saat ini</strong>
                <span class="admin-counter">{{ $showcaseItemForm->media_label }}</span>
              </div>
              <div class="gallery-media-review__stage ppdb-showcase-preview__stage">
                @if ($showcaseItemForm->is_video)
                  <iframe src="{{ $showcaseItemForm->media_url }}" loading="lazy" allowfullscreen title="Preview video PPDB"></iframe>
                @else
                  <img src="{{ $showcaseItemForm->media_url }}" alt="Preview media PPDB">
                @endif
              </div>
            </div>
          @endif

          <div class="admin-inline-actions" style="margin-top: 14px;">
            <button type="submit" class="admin-primary-action">{{ $showcaseFormIsEdit ? 'Simpan Item' : 'Tambah Item' }}</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <script>
    (() => {
      const form = document.querySelector('[data-ppdb-showcase-form]');
      if (!form) return;

      const typeSelect = form.querySelector('[data-ppdb-media-type]');
      const photoBox = form.querySelector('[data-ppdb-media-photo]');
      const urlBox = form.querySelector('[data-ppdb-media-url]');
      const photoInput = form.querySelector('input[name="media_file"]');
      const urlInput = form.querySelector('input[name="media_url"]');

      const syncMediaInputs = () => {
        const isUrl = typeSelect.value === 'video';

        photoBox.hidden = isUrl;
        urlBox.hidden = !isUrl;
        photoInput.disabled = isUrl;
        urlInput.disabled = !isUrl;
      };

      typeSelect.addEventListener('change', syncMediaInputs);
      syncMediaInputs();
    })();
  </script>
@endsection
