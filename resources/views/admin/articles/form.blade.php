@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Artikel' : 'Tambah Artikel',
  'activeAdminPage' => 'artikel',
])

@section('content')

  @include('admin.articles.form.fields')

  @include('admin.articles.form.behavior')

@endsection
