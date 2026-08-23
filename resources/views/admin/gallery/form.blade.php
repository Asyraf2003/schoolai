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
