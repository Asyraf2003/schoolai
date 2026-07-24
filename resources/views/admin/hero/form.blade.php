@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.hero.update', $slide) : route('admin.hero.store');
  $currentType = old('type', $slide->type ?: 'image');
  $currentArticleId = (int) old('article_id', $slide->article_id);
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Hero Slide' : 'Tambah Hero Slide',
  'activeAdminPage' => 'hero',
])

@section('content')

  @include('admin.hero.partials.fields')

  @include('admin.hero.partials.behavior')

@endsection

