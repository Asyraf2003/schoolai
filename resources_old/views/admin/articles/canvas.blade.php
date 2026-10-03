@extends('layouts.article-canvas', ['title' => ($article->admin_title ?: 'Artikel baru').' · Canvas'])

@section('content')
  <div
    class="article-canvas-app"
    data-article-canvas
    data-autosave-url="{{ route('admin.artikel.canvas.autosave', $article) }}"
    data-upload-url="{{ route('admin.artikel.canvas.image', $article) }}"
    data-unsplash-url="{{ route('admin.artikel.canvas.unsplash') }}"
    data-publish-url="{{ route('admin.artikel.canvas.publish', $article) }}"
    data-thumbnail-url="{{ $article->thumbnail_url ?: \App\Models\Article::PLACEHOLDER_THUMBNAIL }}"
  >

    @include('admin.articles.canvas.topbar')

    @include('admin.articles.canvas.workspace')

    @include('admin.articles.canvas.toolbars')

    @include('admin.articles.canvas.dialogs')

    @include('admin.articles.canvas.publish-drawer')

  </div>
@endsection

