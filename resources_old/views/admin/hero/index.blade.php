@extends('layouts.admin', [
  'title' => 'Admin Hero',
  'activeAdminPage' => 'hero',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Opening Hero</h1>
      <p>Opening video selalu tampil pertama. Halaman ini hanya mengatur copy dan CTA Opening; Article Spotlight dikelola terpusat dari halaman Artikel.</p>
    </div>
    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $promotedArticles->count() }} Spotlight aktif</span>
      <a href="{{ route('admin.artikel') }}" class="admin-primary-action admin-primary-action--ghost">Kelola Article Spotlight</a>
    </div>
  </header>

  @if(session('success'))
    <p class="flash-message" role="status">{{ session('success') }}</p>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="admin-error-box" role="alert">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('admin.hero.update') }}" class="gallery-lite-form">
    @csrf
    @method('PUT')

    <section class="gallery-lite-form__panel">
      <h2>Copy Opening Hero</h2>
      <p>Media video tetap dikelola di source/deploy. Kosongkan link CTA jika Opening tidak memerlukan tombol.</p>

      <div class="gallery-lite-form__grid">
        @foreach(['id' => 'Indonesia', 'en' => 'English', 'ar' => 'Arabic'] as $locale => $label)
          <div class="admin-field">
            <label for="eyebrow_{{ $locale }}">Header · {{ $label }}</label>
            <input id="eyebrow_{{ $locale }}" name="eyebrow_{{ $locale }}" value="{{ old('eyebrow_'.$locale, $setting->{'eyebrow_'.$locale}) }}" maxlength="160">
          </div>

          <div class="admin-field admin-field--wide">
            <label for="title_{{ $locale }}">Judul · {{ $label }}</label>
            <input id="title_{{ $locale }}" name="title_{{ $locale }}" value="{{ old('title_'.$locale, $setting->{'title_'.$locale}) }}" maxlength="255" @required($locale === 'id')>
          </div>

          <div class="admin-field admin-field--wide">
            <label for="description_{{ $locale }}">Deskripsi · {{ $label }}</label>
            <textarea id="description_{{ $locale }}" name="description_{{ $locale }}" rows="3" maxlength="2000">{{ old('description_'.$locale, $setting->{'description_'.$locale}) }}</textarea>
          </div>

          <div class="admin-field">
            <label for="cta_label_{{ $locale }}">Label CTA · {{ $label }}</label>
            <input id="cta_label_{{ $locale }}" name="cta_label_{{ $locale }}" value="{{ old('cta_label_'.$locale, $setting->{'cta_label_'.$locale}) }}" maxlength="160">
          </div>
        @endforeach

        <div class="admin-field admin-field--wide">
          <label for="cta_url">Link CTA (opsional)</label>
          <input id="cta_url" name="cta_url" value="{{ old('cta_url', $setting->cta_url) }}" maxlength="2048" placeholder="/ppdb, #program, atau https://...">
          <em>Anchor, path internal, atau URL HTTPS publik. Kosong berarti tanpa CTA.</em>
        </div>
      </div>

      <button type="submit" class="admin-primary-action">Simpan Opening</button>
    </section>
  </form>
@endsection
