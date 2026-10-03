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
