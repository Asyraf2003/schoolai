{{-- ADMIN_PPDB_SETTING_FINAL --}}
@extends('layouts.admin', [
  'title' => 'Admin PPDB',
  'activeAdminPage' => 'ppdb',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Pengaturan PPDB</h1>
      <p>Kelola link formulir pendaftaran publik dan status buka/tutup PPDB. Sederhana, karena kadang software memang tidak perlu pura-pura jadi pesawat luar angkasa.</p>
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
            <label for="registration_url">Link formulir PPDB</label>
            <input
              id="registration_url"
              type="url"
              name="registration_url"
              value="{{ old('registration_url', $setting->registration_url) }}"
              maxlength="2048"
              placeholder="https://forms.gle/..."
              required
            >
            <em>Link ini dipakai oleh tombol daftar pada halaman publik /ppdb. Link lokal, admin, login, dan IP private ditolak.</em>
            @error('registration_url')
              <small>{{ $message }}</small>
            @enderror
          </div>

          <div class="admin-field admin-field--wide">
            <label class="admin-check-field" for="is_active">
              <input
                id="is_active"
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $setting->is_active))
              >
              <span>Aktifkan pendaftaran publik</span>
            </label>
            <em>Jika dimatikan, pengunjung yang menekan tombol daftar akan melihat modal bahwa pendaftaran sedang ditutup.</em>
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
        <p>Gunakan tombol ini kalau cuma mau buka/tutup PPDB tanpa mengubah link formulir.</p>
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
@endsection
