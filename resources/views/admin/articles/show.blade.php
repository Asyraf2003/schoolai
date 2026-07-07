{{-- ADMIN_ARTICLES_SHOW_FINAL --}}
@extends('layouts.admin', [
  'title' => 'Detail Artikel',
  'activeAdminPage' => 'artikel',
])

@section('content')
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

  <section class="gallery-detail-panel gallery-detail-panel--simple">
    <div class="gallery-detail-preview gallery-detail-preview--large">
      @if($article->thumbnail_url)
        <img src="{{ $article->thumbnail_url }}" alt="{{ $article->admin_title }}">
      @else
        <span>Artikel</span>
      @endif
    </div>

    <div class="gallery-detail-summary">
      <div class="gallery-detail-summary__head">
        <div>
          <h1>{{ $article->admin_title }}</h1>
          <p>{{ $article->authorForDisplay() }} · {{ optional($article->published_date)->format('d M Y') ?? '-' }}</p>
        </div>

        <div class="admin-inline-actions">
          <a href="{{ route('admin.artikel') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
          <a href="{{ route('admin.artikel.edit', $article) }}" class="admin-primary-action">Edit</a>
        </div>
      </div>

      <dl class="gallery-detail-list gallery-detail-list--simple">
        <div><dt>Status</dt><dd>Aktif</dd></div>
        <div><dt>Author</dt><dd>{{ $article->authorForDisplay() }}</dd></div>
        <div><dt>Tanggal</dt><dd>{{ optional($article->published_date)->format('d M Y') ?? '-' }}</dd></div>
        <div><dt>Judul English</dt><dd>{{ $article->title_en ?: '-' }}</dd></div>
        <div class="gallery-detail-list__wide"><dt>Thumbnail</dt><dd><a href="{{ $article->thumbnail_url }}" target="_blank" rel="noopener">{{ $article->thumbnail_url }}</a></dd></div>
        <div class="gallery-detail-list__wide"><dt>Link Indonesia</dt><dd><a href="{{ $article->link_id }}" target="_blank" rel="noopener">{{ $article->link_id }}</a></dd></div>
        <div class="gallery-detail-list__wide"><dt>Link English</dt><dd>@if($article->link_en)<a href="{{ $article->link_en }}" target="_blank" rel="noopener">{{ $article->link_en }}</a>@else - @endif</dd></div>
      </dl>

      <section class="gallery-detail-actions gallery-detail-actions--simple">
        <a href="{{ $article->link_id }}" target="_blank" rel="noopener" class="admin-small-action admin-small-action--ghost">Buka Artikel ID</a>

        @if($article->link_en)
          <a href="{{ $article->link_en }}" target="_blank" rel="noopener" class="admin-small-action admin-small-action--ghost">Buka Artikel EN</a>
        @endif

        <form method="POST" action="{{ route('admin.artikel.destroy', $article) }}" data-admin-delete-form data-admin-delete-message="Hapus artikel ini dari website? Link sumber tidak ikut terhapus.">
          @csrf
          @method('DELETE')
          <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">Hapus</button>
        </form>
      </section>
    </div>
  </section>
@endsection
