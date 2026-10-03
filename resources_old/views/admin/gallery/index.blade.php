@extends('layouts.admin', [
  'title' => $page['title'],
  'activeAdminPage' => 'galeri',
])

@section('content')

  @include('admin.gallery.index.header')

  @include('admin.gallery.index.homepage-items')

  @include('admin.gallery.index.page-sections')

@endsection
