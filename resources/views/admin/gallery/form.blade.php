@php
  $page = __('admin.gallery');
  $form = $page['form'];
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.update', $item) : route('admin.galeri.store');
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
  $currentType = old('type', $item->type ?: 'photo');
  $isVideo = $currentType === 'video';

  $languageCompletion = [
    'id' => filled(old('title_id', $item->title_id ?: $item->title)) && filled(old('category_id', $item->category_id ?: $item->category)),
    'en' => filled(old('title_en', $item->title_en)) && filled(old('category_en', $item->category_en)),
    'ar' => filled(old('title_ar', $item->title_ar)) && filled(old('category_ar', $item->category_ar)),
  ];

  $activeLanguage = $errors->hasAny(['title_ar', 'category_ar', 'caption_ar'])
    ? 'ar'
    : ($errors->hasAny(['title_en', 'category_en', 'caption_en']) ? 'en' : 'id');
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? $page['edit_title'] : $page['create_title'],
  'activeAdminPage' => 'galeri',
])

@section('content')

  @include('admin.gallery.form.fields')

  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">

  @include('admin.gallery.form.scripts.form-and-photo')

  @include('admin.gallery.form.scripts.video-preview')

  </script>
@endsection

