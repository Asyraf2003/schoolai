@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.artikel.update', $article) : route('admin.artikel.store');
  $publishedAtValue = old('published_at', optional($article->published_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'));

  $languageCompletion = [
    'id' => filled(old('title_id', $article->title_id)) && filled(old('link_id', $article->link_id)),
    'en' => filled(old('title_en', $article->title_en)) && filled(old('link_en', $article->link_en)),
    'ar' => filled(old('title_ar', $article->title_ar)) && filled(old('link_ar', $article->link_ar)),
  ];

  $activeLanguage = $errors->hasAny(['title_ar', 'description_ar', 'link_ar'])
    ? 'ar'
    : ($errors->hasAny(['title_en', 'description_en', 'link_en']) ? 'en' : 'id');
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Artikel' : 'Tambah Artikel',
  'activeAdminPage' => 'artikel',
])

@section('content')

  @include('admin.articles.form.fields')

  @include('admin.articles.form.behavior')

@endsection

