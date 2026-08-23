@extends('layouts.admin', [
  'title' => 'Admin PPDB',
  'activeAdminPage' => 'ppdb',
])

@section('content')

  @include('admin.ppdb.edit.styles')

  @include('admin.ppdb.edit.overview')

  @include('admin.ppdb.edit.showcase')

  @include('admin.ppdb.edit.behavior')

@endsection
