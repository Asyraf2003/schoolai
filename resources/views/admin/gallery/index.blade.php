@php
  $page = __('admin.gallery');
  $activePageSections = collect($pageSections ?? []);
  $homepageLimit = $limits['max_items'] ?? 6;
@endphp

@extends('layouts.admin', [
  'title' => $page['title'],
  'activeAdminPage' => 'galeri',
])

@section('content')

  @include('admin.gallery.index.header')

  @include('admin.gallery.index.homepage-items')

  @include('admin.gallery.index.page-sections')

@endsection

