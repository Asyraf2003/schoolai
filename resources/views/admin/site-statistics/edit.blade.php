@extends('layouts.admin', [
  'title' => 'Admin Statistik Homepage',
  'activeAdminPage' => 'stats',
])

@section('content')

  @include('admin.site-statistics.edit.header')

  <div class="admin-stat-index">

    @include('admin.site-statistics.edit.create-form')

    @include('admin.site-statistics.edit.statistics-list')

  </div>

  @include('admin.site-statistics.edit.behavior')

@endsection
