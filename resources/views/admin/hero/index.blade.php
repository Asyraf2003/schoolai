@extends('layouts.admin', [
  'title' => 'Admin Hero',
  'activeAdminPage' => 'hero',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Hero Homepage</h1>
      <p>Opening selalu tampil pertama dengan video sekolah yang fixed. Halaman ini hanya mengubah copy/CTA dan memilih Article setelah Opening.</p>
    </div>
    <span class="admin-counter">{{ $promotedArticles->count() }} artikel dipromosikan</span>
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
      <p>Media tidak dapat diganti dari admin. Kosongkan link CTA jika Opening tidak perlu tombol.</p>

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

  <section class="admin-gallery-block" aria-label="Artikel yang dipromosikan di Hero">
    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h2>Article setelah Opening</h2>
        <p>Title, excerpt, cover, dan URL dibaca langsung dari Article saat homepage dimuat.</p>
      </div>

      @if($articleOptions->isNotEmpty())
        <form method="POST" action="{{ route('admin.hero.articles.promote') }}" class="admin-inline-actions">
          @csrf
          <label class="sr-only" for="article_id">Pilih artikel terbit</label>
          <select id="article_id" name="article_id" required>
            <option value="">Pilih artikel terbit</option>
            @foreach($articleOptions as $articleOption)
              <option value="{{ $articleOption->getKey() }}">{{ $articleOption->admin_title }}</option>
            @endforeach
          </select>
          <button type="submit" class="admin-primary-action">Promosikan</button>
        </form>
      @endif
    </header>

    @if($promotedArticles->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($promotedArticles as $article)
          <article class="gallery-lite-row">
            <span class="gallery-lite-row__order">{{ str_pad((string) $article->hero_position, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="gallery-lite-row__body">
              <strong>{{ $article->admin_title }}</strong>
              <small>Article #{{ $article->getKey() }} · {{ $article->statusLabel() }}</small>
            </span>
            <span class="gallery-lite-actions">
              <form method="POST" action="{{ route('admin.hero.articles.move-up', $article) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->first)>Naik</button>
              </form>
              <form method="POST" action="{{ route('admin.hero.articles.move-down', $article) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->last)>Turun</button>
              </form>
              <form method="POST" action="{{ route('admin.hero.articles.unpromote', $article) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-small-action admin-small-action--danger">Lepas</button>
              </form>
            </span>
          </article>
        @endforeach
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>Hero hanya berisi Opening.</h2>
        <p>Tidak ada Article yang otomatis masuk Hero.</p>
      </div>
    @endif
  </section>
@endsection
